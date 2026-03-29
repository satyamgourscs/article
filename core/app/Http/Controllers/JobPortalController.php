<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\PostedJob;
use Illuminate\Http\Request;

class JobPortalController extends Controller
{
    public function index()
    {
        if (! jobPortalSchemaReady()) {
            return response()->view('Template::job_portal.setup_required', [
                'pageTitle' => __('Job portal setup'),
            ], 503);
        }

        $pageTitle = __('Jobs');
        $query = PostedJob::query()
            ->openForListing()
            ->with('buyer')
            ->whereHas('buyer', fn ($q) => $q->active());

        $search = request('search');
        if (is_string($search) && trim($search) !== '') {
            $term = '%'.trim($search).'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('domain', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('company_name', 'like', $term);
            });
        }

        $jobs = $query->latest()->paginate(getPaginate());

        return view('Template::job_portal.index', compact('pageTitle', 'jobs'));
    }

    public function show(PostedJob $postedJob)
    {
        if (! jobPortalSchemaReady()) {
            return response()->view('Template::job_portal.setup_required', [
                'pageTitle' => __('Job portal setup'),
            ], 503);
        }

        abort_unless($postedJob->status === PostedJob::STATUS_OPEN, 404);
        $postedJob->load('buyer');
        $pageTitle = $postedJob->title;
        $alreadyApplied = auth()->check()
            ? JobApplication::where('job_id', $postedJob->id)->where('user_id', auth()->id())->exists()
            : false;

        return view('Template::job_portal.show', compact('pageTitle', 'postedJob', 'alreadyApplied'));
    }

    public function apply(Request $request)
    {
        if (! jobPortalSchemaReady()) {
            return response()->view('Template::job_portal.setup_required', [
                'pageTitle' => __('Job portal setup'),
            ], 503);
        }

        $data = $request->validate([
            'job_id' => 'required|exists:posted_jobs,id',
        ]);

        if (! auth()->check()) {
            $notify[] = ['error', __('Please log in as a student to apply.')];

            return to_route('user.login')->withNotify($notify);
        }

        if (auth()->guard('buyer')->check()) {
            $notify[] = ['error', __('Firms cannot apply to jobs.')];

            return back()->withNotify($notify);
        }

        $job = PostedJob::where('id', $data['job_id'])->firstOrFail();
        if ($job->status !== PostedJob::STATUS_OPEN) {
            $notify[] = ['error', __('This job is not open for applications.')];

            return back()->withNotify($notify);
        }

        if (JobApplication::where('job_id', $job->id)->where('user_id', auth()->id())->exists()) {
            $notify[] = ['error', __('You already applied to this job.')];

            return back()->withNotify($notify);
        }

        JobApplication::create([
            'job_id' => $job->id,
            'user_id' => auth()->id(),
            'applied_at' => now(),
            'status' => JobApplication::STATUS_APPLIED,
        ]);

        $notify[] = ['success', __('Application submitted.')];

        return back()->withNotify($notify);
    }
}
