@extends('admin.layouts.app')
@section('panel')
    @php $edit = isset($plan); @endphp
    <div class="card">
        <div class="card-body">
            <form action="{{ $edit ? route('admin.plans.update', $plan->id) : route('admin.plans.store') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">@lang('Name')</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">@lang('Type')</label>
                        <select name="type" class="form-control" required>
                            <option value="student" @selected(old('type', $plan->type ?? '') === 'student')>@lang('Student')</option>
                            <option value="company" @selected(old('type', $plan->type ?? '') === 'company')>@lang('Firm')</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Price')</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $plan->price ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Duration (days)')</label>
                        <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $plan->duration_days ?? 365) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Active')</label>
                        <select name="is_active" class="form-control">
                            <option value="1" @selected(old('is_active', $plan->is_active ?? 1) == 1)>@lang('Yes')</option>
                            <option value="0" @selected(old('is_active', $plan->is_active ?? 1) == 0)>@lang('No')</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('job_apply_limit') <small>(999999 = ∞)</small></label>
                        <input type="number" name="job_apply_limit" class="form-control" value="{{ old('job_apply_limit', $plan->job_apply_limit ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('job_view_limit') <small>(0 or 999999 = ∞)</small></label>
                        <input type="number" name="job_view_limit" class="form-control" value="{{ old('job_view_limit', $plan->job_view_limit ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('job_post_limit') <small>(999999 = ∞)</small></label>
                        <input type="number" name="job_post_limit" class="form-control" value="{{ old('job_post_limit', $plan->job_post_limit ?? 0) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">@lang('listing_visible_jobs') <small>(999999 = full list)</small></label>
                        <input type="number" name="listing_visible_jobs" class="form-control" value="{{ old('listing_visible_jobs', $plan->listing_visible_jobs ?? 2) }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn--primary mt-3">@lang('Save')</button>
                <a href="{{ route('admin.plans.index') }}" class="btn btn--dark mt-3">@lang('Back')</a>
            </form>
        </div>
    </div>
@endsection
