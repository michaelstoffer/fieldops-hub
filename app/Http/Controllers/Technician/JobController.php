<?php

namespace App\Http\Controllers\Technician;

use App\Events\JobStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Item;
use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobLineItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Response;
use Inertia\ResponseFactory;

class JobController extends Controller
{
    // -------------------------------------------------------------------------
    // Inertia pages
    // -------------------------------------------------------------------------

    public function index(Request $request): Response|ResponseFactory
    {
        $jobs = $this->todayQuery($request->user()->id)
            ->with(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems'])
            ->orderBy('scheduled_at')
            ->get();

        return inertia('Technician/Jobs/Index', [
            'jobs' => $jobs,
            'statuses' => Job::statuses(),
        ]);
    }

    public function show(Request $request, Job $job): Response|ResponseFactory
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $job->load(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems', 'messages']);

        return inertia('Technician/Jobs/Show', [
            'job'      => $job,
            'statuses' => Job::statuses(),
        ]);
    }

    // -------------------------------------------------------------------------
    // JSON API endpoints (used by PWA / service worker cache)
    // -------------------------------------------------------------------------

    public function today(Request $request): JsonResponse
    {
        $jobs = $this->todayQuery($request->user()->id)
            ->with(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems'])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json(['data' => $jobs]);
    }

    public function apiShow(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $job->load(['customer', 'property', 'jobType', 'checklistItems', 'attachments', 'lineItems']);

        return response()->json(['data' => $job]);
    }

    public function updateStatus(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $allowedStatuses = array_diff(
            array_keys(Job::statuses()),
            [Job::STATUS_CANCELLED]
        );

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);

        $timestamps = match ($request->status) {
            Job::STATUS_IN_PROGRESS => [
                'arrived_at' => $job->arrived_at ?? now(),
                'started_at' => $job->started_at ?? now(),
            ],
            Job::STATUS_COMPLETED => ['completed_at' => now()],
            default => [],
        };

        $oldStatus = $job->status;
        $job->update(['status' => $request->status, ...$timestamps]);

        JobStatusChanged::dispatch($job->fresh(), $oldStatus, $request->status);

        return response()->json(['status' => 'ok', 'data' => $job->fresh()]);
    }

    public function updateNotes(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'technician_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $job->update(['technician_notes' => $request->technician_notes]);

        return response()->json(['status' => 'ok', 'data' => $job->fresh()]);
    }

    public function updateCustomerNotes(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'customer_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $job->update(['customer_notes' => $request->customer_notes]);

        return response()->json(['status' => 'ok', 'data' => $job->fresh()]);
    }

    public function toggleChecklistItem(Request $request, Job $job, JobChecklistItem $item): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);
        abort_unless($item->job_id === $job->id, 404);

        $request->validate([
            'completed' => ['required', 'boolean'],
        ]);

        $item->update([
            'completed_at' => $request->boolean('completed') ? now() : null,
        ]);

        return response()->json(['status' => 'ok', 'data' => $item->fresh()]);
    }

    public function uploadPhoto(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $request->validate([
            'photo' => ['required', 'file', 'image', 'max:10240'], // 10 MB max (client compresses first)
            'tag'   => ['nullable', Rule::in(['before', 'after'])],
        ]);

        $disk = config('filesystems.attachment_disk', 'public');
        $file = $request->file('photo');
        $path = $file->store("jobs/{$job->id}/photos", $disk);

        $attachment = $job->attachments()->create([
            'organization_id' => $job->organization_id,
            'uploaded_by'     => $request->user()->id,
            'filename'        => $file->getClientOriginalName(),
            'disk'            => $disk,
            'path'            => $path,
            'mime_type'       => $file->getMimeType(),
            'size'            => $file->getSize(),
            'tag'             => $request->input('tag'),
        ]);

        return response()->json(['status' => 'ok', 'data' => $attachment], 201);
    }

    public function deletePhoto(Request $request, Job $job, Attachment $attachment): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);
        abort_unless($attachment->attachable_type === Job::class && $attachment->attachable_id === $job->id, 404);

        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        return response()->json(['status' => 'ok']);
    }

    public function addLineItem(Request $request, Job $job): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);

        $data = $request->validate([
            'item_id'    => ['nullable', 'integer', 'exists:items,id'],
            'name'       => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'quantity'   => ['required', 'numeric', 'min:0.001'],
        ]);

        // If linking a catalog item, snapshot its current price unless caller overrides
        if (! empty($data['item_id'])) {
            $catalogItem = Item::find($data['item_id']);
            if ($catalogItem && ! $request->has('unit_price')) {
                $data['unit_price'] = $catalogItem->unit_price;
            }
        }

        $data['sort_order'] = $job->lineItems()->max('sort_order') + 1;

        $lineItem = $job->lineItems()->create($data);

        return response()->json(['status' => 'ok', 'data' => $lineItem->fresh()], 201);
    }

    public function updateLineItem(Request $request, Job $job, JobLineItem $lineItem): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);
        abort_unless($lineItem->job_id === $job->id, 404);

        $data = $request->validate([
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'unit_price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'quantity'   => ['sometimes', 'required', 'numeric', 'min:0.001'],
        ]);

        $lineItem->update($data);

        return response()->json(['status' => 'ok', 'data' => $lineItem->fresh()]);
    }

    public function deleteLineItem(Request $request, Job $job, JobLineItem $lineItem): JsonResponse
    {
        abort_unless($job->assigned_to === $request->user()->id, 403);
        abort_unless($lineItem->job_id === $job->id, 404);

        $lineItem->delete();

        return response()->json(['status' => 'ok']);
    }

    public function catalogItems(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Item::where('organization_id', $request->user()->organization_id)
            ->where('is_active', true)
            ->orderBy('name');

        if ($q = $request->input('q')) {
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        return response()->json(['data' => $query->limit(20)->get()]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function todayQuery(int $userId)
    {
        return Job::where('assigned_to', $userId)
            ->whereDate('scheduled_at', today())
            ->whereNotIn('status', [Job::STATUS_CANCELLED]);
    }
}
