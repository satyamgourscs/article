<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Support\SafeSchema;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    private function redirectUnlessPlansTable(): ?\Illuminate\Http\RedirectResponse
    {
        if (! SafeSchema::hasTable('plans')) {
            $notify[] = ['warning', __('The plans table is missing. Run php artisan migrate from the core directory.')];

            return to_route('admin.dashboard')->withNotify($notify);
        }

        return null;
    }

    public function index()
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        $pageTitle = 'Subscription Plans';
        $plans = Plan::orderBy('type')->orderBy('price')->paginate(getPaginate());

        return view('admin.plans.index', compact('pageTitle', 'plans'));
    }

    public function create()
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        $pageTitle = 'Add Plan';

        return view('admin.plans.form', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        return $this->savePlan($request);
    }

    public function edit($id)
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        $pageTitle = 'Edit Plan';
        $plan = Plan::findOrFail($id);

        return view('admin.plans.form', compact('pageTitle', 'plan'));
    }

    public function update(Request $request, $id)
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        return $this->savePlan($request, $id);
    }

    private function savePlan(Request $request, $id = 0)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'type' => 'required|in:student,company',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'job_apply_limit' => 'required|integer|min:0',
            'job_view_limit' => 'required|integer|min:0',
            'job_post_limit' => 'required|integer|min:0',
            'listing_visible_jobs' => 'required|integer|min:0',
            'is_active' => 'nullable|in:0,1',
        ]);

        if ($id) {
            $plan = Plan::findOrFail($id);
            $notifyMsg = 'Plan updated successfully';
        } else {
            $plan = new Plan;
            $notifyMsg = 'Plan created successfully';
        }

        $plan->name = $request->name;
        $plan->type = $request->type;
        $plan->price = $request->price;
        $plan->duration_days = $request->duration_days;
        $plan->job_apply_limit = $request->job_apply_limit;
        $plan->job_view_limit = $request->job_view_limit;
        $plan->job_post_limit = $request->job_post_limit;
        $plan->listing_visible_jobs = $request->listing_visible_jobs;
        $plan->is_active = (bool) $request->is_active;
        $plan->save();

        $notify[] = ['success', $notifyMsg];

        return to_route('admin.plans.index')->withNotify($notify);
    }

    public function status(Request $request, $id)
    {
        if ($r = $this->redirectUnlessPlansTable()) {
            return $r;
        }

        $request->validate(['status' => 'required|in:0,1']);
        $plan = Plan::findOrFail($id);
        $plan->is_active = (bool) (int) $request->status;
        $plan->save();
        $notify[] = ['success', 'Plan status updated'];

        return back()->withNotify($notify);
    }
}
