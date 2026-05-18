<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\JobType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class JobTypeController extends Controller
{
    public function index(Request $request): Response|ResponseFactory
    {
        $jobTypes = JobType::where('organization_id', $request->user()->organization_id)
            ->orderBy('name')
            ->get(['id', 'name', 'color', 'description', 'is_active']);

        return inertia('Owner/JobTypes/Index', [
            'jobTypes' => $jobTypes,
        ]);
    }

    public function create(): Response|ResponseFactory
    {
        return inertia('Owner/JobTypes/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'color'       => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        JobType::create([
            ...$data,
            'organization_id' => $request->user()->organization_id,
            'is_active'       => $data['is_active'] ?? true,
        ]);

        return redirect()->route('owner.job-types.index')
            ->with('success', 'Job type created.');
    }

    public function edit(Request $request, JobType $jobType): Response|ResponseFactory
    {
        abort_unless($jobType->organization_id === $request->user()->organization_id, 403);

        return inertia('Owner/JobTypes/Edit', [
            'jobType' => $jobType->only(['id', 'name', 'color', 'description', 'is_active']),
        ]);
    }

    public function update(Request $request, JobType $jobType): RedirectResponse
    {
        abort_unless($jobType->organization_id === $request->user()->organization_id, 403);

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'color'       => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        $jobType->update($data);

        return redirect()->route('owner.job-types.index')
            ->with('success', 'Job type updated.');
    }

    public function quickCreate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'max:50'],
        ]);

        $jobType = JobType::create([
            ...$data,
            'organization_id' => $request->user()->organization_id,
            'is_active'       => true,
        ]);

        return response()->json([
            'id'    => $jobType->id,
            'name'  => $jobType->name,
            'color' => $jobType->color,
        ], 201);
    }

    public function destroy(Request $request, JobType $jobType): RedirectResponse
    {
        abort_unless($jobType->organization_id === $request->user()->organization_id, 403);

        $jobType->update(['is_active' => false]);

        return redirect()->route('owner.job-types.index')
            ->with('success', 'Job type deactivated.');
    }
}
