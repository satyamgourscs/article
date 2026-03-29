@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h5>{{ __($pageTitle) }}</h5>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.plans.create') }}" class="btn btn-sm btn--primary"><i class="las la-plus"></i> @lang('Add Plan')</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Duration (days)')</th>
                            <th>@lang('Apply / View / Post')</th>
                            <th>@lang('List preview')</th>
                            <th>@lang('Active')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plans as $plan)
                            <tr>
                                <td>{{ __($plan->name) }}</td>
                                <td>{{ __($plan->type) }}</td>
                                <td>{{ showAmount($plan->price) }}</td>
                                <td>{{ $plan->duration_days }}</td>
                                <td>{{ $plan->job_apply_limit }} / {{ $plan->job_view_limit }} / {{ $plan->job_post_limit }}</td>
                                <td>{{ $plan->listing_visible_jobs }}</td>
                                <td>
                                    @if ($plan->is_active)
                                        <span class="badge badge--success">@lang('Yes')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('No')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-pen"></i></a>
                                    <form action="{{ route('admin.plans.status', $plan->id) }}" method="post" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $plan->is_active ? 0 : 1 }}">
                                        <button type="submit" class="btn btn-sm btn-outline--dark">@lang('Toggle')</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">@lang('No plans')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($plans->hasPages())
            <div class="card-footer">{{ $plans->links() }}</div>
        @endif
    </div>
@endsection
