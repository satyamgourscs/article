<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\PostedJob;
use Illuminate\Http\Request;

class PostedJobPortalController extends Controller
{
    public function index()
    {
        if (! jobPortalSchemaReady()) {
            $notify[] = ['warning', __('Job portal tables are missing. Run php artisan migrate from the core directory.')];

            return to_route('admin.dashboard')->withNotify($notify);
        }

        $pageTitle = __('Posted jobs (portal)');
        $jobs = PostedJob::query()->with('buyer')->latest()->paginate(getPaginate());

        return view('admin.posted_jobs.index', compact('pageTitle', 'jobs'));
    }

    public function applications()
    {
        if (! jobPortalSchemaReady()) {
            $notify[] = ['warning', __('Job portal tables are missing. Run php artisan migrate from the core directory.')];

            return to_route('admin.dashboard')->withNotify($notify);
        }

        $pageTitle = __('Job applications');
        $applications = JobApplication::query()
            ->with(['postedJob.buyer', 'user'])
            ->latest('applied_at')
            ->paginate(getPaginate());

        return view('admin.posted_jobs.applications', compact('pageTitle', 'applications'));
    }

    public function destroy(PostedJob $postedJob)
    {
        $postedJob->delete();
        $notify[] = ['success', __('Job deleted.')];

        return back()->withNotify($notify);
    }

    public function applicationStatus(Request $request, JobApplication $jobApplication)
    {
        $request->validate([
            'status' => 'required|in:'.JobApplication::STATUS_APPLIED.','.JobApplication::STATUS_SELECTED.','.JobApplication::STATUS_REJECTED,
        ]);
        $jobApplication->update(['status' => $request->status]);
        $notify[] = ['success', __('Application updated.')];

        return back()->withNotify($notify);
    }
}
