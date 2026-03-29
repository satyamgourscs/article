<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;

class PortalJobApplicationController extends Controller
{
    public function index()
    {
        if (! jobPortalSchemaReady()) {
            return response()->view('Template::job_portal.setup_required', [
                'pageTitle' => __('Job portal setup'),
            ], 503);
        }

        $pageTitle = __('My applications');
        $applications = JobApplication::query()
            ->where('user_id', auth()->id())
            ->with('postedJob.buyer')
            ->latest('applied_at')
            ->paginate(getPaginate());

        return view('Template::job_portal.student.applications', compact('pageTitle', 'applications'));
    }
}
