<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\PostedJob;
use Illuminate\Http\Request;

class PostedJobController extends Controller
{
    protected function ensureJobPortalSchema(): ?\Illuminate\Http\Response
    {
        if (! jobPortalSchemaReady()) {
            return response()->view('Template::job_portal.setup_required', [
                'pageTitle' => __('Job portal setup'),
            ], 503);
        }

        return null;
    }

    public function index()
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $pageTitle = __('My posted jobs');
        $jobs = PostedJob::query()
            ->where('buyer_id', auth()->guard('buyer')->id())
            ->latest()
            ->paginate(getPaginate());

        return view('Template::job_portal.firm.index', compact('pageTitle', 'jobs'));
    }

    public function create()
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $pageTitle = __('Post job');

        return view('Template::job_portal.firm.form', ['pageTitle' => $pageTitle, 'job' => null]);
    }

    public function store(Request $request)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $job = $this->savePostedJob($request, null);

        $notify[] = ['success', __('Job posted.')];

        return to_route('buyer.firm.posted_jobs.show', $job->id)->withNotify($notify);
    }

    public function show(PostedJob $postedJob)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        $pageTitle = $postedJob->title;
        $postedJob->load(['applications.user']);

        return view('Template::job_portal.firm.show', compact('pageTitle', 'postedJob'));
    }

    public function edit(PostedJob $postedJob)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        $pageTitle = __('Edit job');

        return view('Template::job_portal.firm.form', compact('pageTitle', 'job'));
    }

    public function update(Request $request, PostedJob $postedJob)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        $this->savePostedJob($request, $postedJob);
        $notify[] = ['success', __('Job updated.')];

        return to_route('buyer.firm.posted_jobs.show', $postedJob->id)->withNotify($notify);
    }

    public function destroy(PostedJob $postedJob)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        $postedJob->delete();
        $notify[] = ['success', __('Job removed.')];

        return to_route('buyer.firm.posted_jobs.index')->withNotify($notify);
    }

    public function markFilled(PostedJob $postedJob)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        $postedJob->update(['status' => PostedJob::STATUS_FILLED]);
        $notify[] = ['success', __('Job marked as filled.')];

        return back()->withNotify($notify);
    }

    public function applicationStatus(Request $request, PostedJob $postedJob, JobApplication $jobApplication)
    {
        if ($fallback = $this->ensureJobPortalSchema()) {
            return $fallback;
        }

        $this->authorizePostedJob($postedJob);
        abort_unless((int) $jobApplication->job_id === (int) $postedJob->id, 404);

        $request->validate([
            'status' => 'required|in:'.JobApplication::STATUS_APPLIED.','.JobApplication::STATUS_SELECTED.','.JobApplication::STATUS_REJECTED,
        ]);

        $jobApplication->update(['status' => $request->status]);
        $notify[] = ['success', __('Application updated.')];

        return back()->withNotify($notify);
    }

    protected function authorizePostedJob(PostedJob $postedJob): void
    {
        abort_unless((int) $postedJob->buyer_id === (int) auth()->guard('buyer')->id(), 403);
    }

    protected function savePostedJob(Request $request, ?PostedJob $existing): PostedJob
    {
        $type = $request->input('type');
        $rules = [
            'type' => 'required|in:'.PostedJob::TYPE_ARTICLESHIP.','.PostedJob::TYPE_SHORT_TERM,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'domain' => 'nullable|string|max:191',
            'location' => 'nullable|string|max:191',
            'company_name' => 'nullable|string|max:191',
        ];

        if ($type === PostedJob::TYPE_ARTICLESHIP) {
            $rules['domain'] = 'required|string|max:191';
            $rules['stipend'] = 'nullable|numeric|min:0';
            $rules['open_positions'] = 'nullable|integer|min:0';
            $rules['current_articles'] = 'nullable|integer|min:0';
            $rules['location'] = 'required|string|max:191';
            $rules['company_name'] = 'required|string|max:191';
        } else {
            $rules['per_day_pay'] = 'nullable|numeric|min:0';
            $rules['duration'] = 'required|string|max:191';
            $rules['domain'] = 'required|string|max:191';
            $rules['location'] = 'required|string|max:191';
        }

        $data = $request->validate($rules);
        $payload = [
            'title' => $data['title'],
            'type' => $data['type'],
            'domain' => $data['domain'] ?? null,
            'location' => $data['location'] ?? null,
            'company_name' => $data['type'] === PostedJob::TYPE_ARTICLESHIP ? ($data['company_name'] ?? null) : null,
            'description' => $data['description'] ?? null,
            'stipend' => $data['type'] === PostedJob::TYPE_ARTICLESHIP ? ($data['stipend'] ?? null) : null,
            'per_day_pay' => $data['type'] === PostedJob::TYPE_SHORT_TERM ? ($data['per_day_pay'] ?? null) : null,
            'duration' => $data['type'] === PostedJob::TYPE_SHORT_TERM ? ($data['duration'] ?? null) : null,
            'open_positions' => $data['type'] === PostedJob::TYPE_ARTICLESHIP ? ($data['open_positions'] ?? null) : null,
            'current_articles' => $data['type'] === PostedJob::TYPE_ARTICLESHIP ? ($data['current_articles'] ?? null) : null,
            'buyer_id' => auth()->guard('buyer')->id(),
            'status' => PostedJob::STATUS_OPEN,
        ];

        if ($existing) {
            unset($payload['buyer_id'], $payload['status']);
            $existing->update($payload);

            return $existing->fresh();
        }

        return PostedJob::create($payload);
    }
}
