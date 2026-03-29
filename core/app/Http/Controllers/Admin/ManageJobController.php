<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class ManageJobController extends Controller
{

    public function allJobs($catId = 0)
    {
        $pageTitle = 'All Opportunities';
        $jobs = $this->jobData(catId:$catId);
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }

    public function approvedJobs()
    {
        $pageTitle = 'Approved Opportunities';
        $jobs = $this->jobData('approved');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }


    public function pendingJobs()
    {
        $pageTitle = 'Pending Opportunities';
        $jobs = $this->jobData('pending');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }

    public function rejectedJobs()
    {
        $pageTitle = 'Rejected|Unverified Opportunities';
        $jobs = $this->jobData('rejected');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }



    public function publishedJobs()
    {
        $pageTitle = 'Published|Verified Opportunities';
        $jobs = $this->jobData('published');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }


    public function draftedJobs()
    {
        $pageTitle = 'Drafted Opportunities';
        $jobs = $this->jobData('drafted');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }
    public function processingJobs()
    {
        $pageTitle = 'Processing Opportunities';
        $jobs = $this->jobData('processing');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }
    public function completedJobs()
    {
        $pageTitle = 'Completed Opportunities';
        $jobs = $this->jobData('completed');
        return view('admin.jobs.list', compact('pageTitle', 'jobs'));
    }


    protected function jobData($scope = null, $catId = 0)
    {
        $jobs = Job::query();
        if ($scope) {
            $jobs = Job::$scope();
            if ($scope == "pending") {
                $jobs->where('status', Status::JOB_PUBLISH);
            }
        }
        if ($catId) {
            $jobs =  $jobs->where('category_id', $catId);
        }
        return $jobs->with(['buyer', 'category', 'subcategory', 'bids'])->searchable(['buyer:username', 'title', 'budget', 'category:name', 'subcategory:name'])->orderBy('id', 'desc')->paginate(getPaginate());
    }


    public function detail($id)
    {
        $job = Job::with(['skills', 'buyer', 'category', 'subcategory'])->findOrFail($id);
        $pageTitle = 'Opportunity Detail of Firm - ' . ($job->buyer->fullname ?? 'N/A');

        $widget['total_bid'] = (clone $job)->bids()->count();
        $widget['total_interview'] = (clone $job)->interviews;

        $project = (clone $job)->project;
        if ($project) {
            $assignFreelancer = $project->where('status', Status::PROJECT_RUNNING)->with('user', 'buyer')->first();
            $widget['assign_freelancer'] = $assignFreelancer->user ?? '';
        }
        $widget['assign_freelancer'] = '';

        return view('admin.jobs.detail', compact('pageTitle', 'job', 'widget'));
    }

    public function jobApprove($id)
    {
        $job = Job::with('buyer')->findOrFail($id);
        $job->is_approved = Status::JOB_APPROVED;
        $job->save();
        notify($job->buyer, 'JOB_APPROVED', [
            'job' => $job->title
        ]);
        $notify[] = ['success', 'Opportunity approved successfully'];
        return to_route('admin.jobs.approved')->withNotify($notify);
    }

    public function jobReject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required'
        ]);
        $job = Job::with('buyer')->findOrFail($id);
        $job->is_approved = Status::JOB_REJECTED;
        $job->status = Status::JOB_FINISHED;
        $job->rejection_reason = $request->reason;
        $job->save();

        notify($job->buyer, 'JOB_REJECTED', [
            'job' => $job->title,
            'reason' => $request->reason
        ]);

        $notify[] = ['success', 'Opportunity rejected successfully'];
        return to_route('admin.jobs.rejected')->withNotify($notify);
    }
}
