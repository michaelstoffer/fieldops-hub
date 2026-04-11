<?php

use App\Models\Attachment;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function attachmentDisk(): string
{
    return config('filesystems.attachment_disk', 'public');
}

function photoSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id]);

    return [$technician, $org, $customer];
}

// ── Upload ───────────────────────────────────────────────────────────────────

test('technician can upload a photo to their job', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $file = UploadedFile::fake()->image('before.jpg', 800, 600);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => $file,
            'tag'   => 'before',
        ])
        ->assertCreated()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('data.tag', 'before')
        ->assertJsonPath('data.disk', attachmentDisk());

    $saved = $job->attachments()->first();
    expect($saved->disk)->toBe(attachmentDisk());
    Storage::disk(attachmentDisk())->assertExists($saved->path);
});

test('photo is stored with after tag', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => UploadedFile::fake()->image('after.jpg'),
            'tag'   => 'after',
        ])
        ->assertCreated()
        ->assertJsonPath('data.tag', 'after');
});

test('tag is optional — photo can be uploaded without a tag', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => UploadedFile::fake()->image('shot.jpg'),
        ])
        ->assertCreated()
        ->assertJsonPath('data.tag', null);
});

test('upload rejects an invalid tag value', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => UploadedFile::fake()->image('shot.jpg'),
            'tag'   => 'during',
        ])
        ->assertUnprocessable();
});

test('upload rejects a non-image file', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => UploadedFile::fake()->create('doc.pdf', 500, 'application/pdf'),
        ])
        ->assertUnprocessable();
});

test('technician cannot upload a photo to another technician\'s job', function () {
    Storage::fake(attachmentDisk());
    [$technician, $org, $customer] = photoSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/photos", [
            'photo' => UploadedFile::fake()->image('x.jpg'),
        ])
        ->assertForbidden();
});

// ── Delete ───────────────────────────────────────────────────────────────────

test('technician can delete a photo from their job', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    // Upload first
    $disk = attachmentDisk();
    $file = UploadedFile::fake()->image('photo.jpg');
    $path = $file->store("jobs/{$job->id}/photos", $disk);

    $attachment = $job->attachments()->create([
        'organization_id' => $job->organization_id,
        'uploaded_by'     => $technician->id,
        'filename'        => 'photo.jpg',
        'disk'            => $disk,
        'path'            => $path,
        'mime_type'       => 'image/jpeg',
        'size'            => 1000,
        'tag'             => 'before',
    ]);

    Storage::disk($disk)->assertExists($path);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$job->id}/photos/{$attachment->id}")
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($job->attachments()->count())->toBe(0);
    Storage::disk($disk)->assertMissing($path);
});

test('technician cannot delete a photo from another technician\'s job', function () {
    Storage::fake(attachmentDisk());
    [$technician, $org, $customer] = photoSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $attachment = $job->attachments()->create([
        'organization_id' => $job->organization_id,
        'uploaded_by'     => $other->id,
        'filename'        => 'photo.jpg',
        'disk'            => attachmentDisk(),
        'path'            => 'jobs/1/photos/x.jpg',
        'mime_type'       => 'image/jpeg',
        'size'            => 1000,
    ]);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$job->id}/photos/{$attachment->id}")
        ->assertForbidden();
});

test('delete returns 404 when attachment does not belong to the job', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $jobA = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);
    $jobB = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $attachmentFromB = $jobB->attachments()->create([
        'organization_id' => $jobB->organization_id,
        'uploaded_by'     => $technician->id,
        'filename'        => 'photo.jpg',
        'disk'            => attachmentDisk(),
        'path'            => 'jobs/2/photos/x.jpg',
        'mime_type'       => 'image/jpeg',
        'size'            => 1000,
    ]);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$jobA->id}/photos/{$attachmentFromB->id}")
        ->assertNotFound();
});

// ── Show includes attachments ─────────────────────────────────────────────────

test('api show response includes job attachments', function () {
    Storage::fake(attachmentDisk());
    [$technician, , $customer] = photoSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $job->attachments()->createMany([
        ['organization_id' => $job->organization_id, 'uploaded_by' => $technician->id, 'filename' => 'a.jpg', 'disk' => 'public', 'path' => 'a.jpg', 'mime_type' => 'image/jpeg', 'size' => 100, 'tag' => 'before'],
        ['organization_id' => $job->organization_id, 'uploaded_by' => $technician->id, 'filename' => 'b.jpg', 'disk' => 'public', 'path' => 'b.jpg', 'mime_type' => 'image/jpeg', 'size' => 100, 'tag' => 'after'],
    ]);

    $this->actingAs($technician)
        ->getJson("/api/technician/jobs/{$job->id}")
        ->assertOk()
        ->assertJsonCount(2, 'data.attachments');
});
