@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-3">
        <div class="col-md-12">
            <h5>{{ __($pageTitle) }}</h5>
        </div>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two">
                    <thead>
                        <tr>
                            <th>@lang('ID')</th>
                            <th>@lang('Title')</th>
                            <th>@lang('Firm')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr>
                                <td>{{ $job->id }}</td>
                                <td>{{ __($job->title) }}</td>
                                <td>{{ '@'.($job->buyer->username ?? '') }}</td>
                                <td>{{ $job->type }}</td>
                                <td>{{ __($job->status) }}</td>
                                <td>
                                    <form action="{{ route('admin.posted.portal.destroy', $job) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm(@json(__('Delete?')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Delete')</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-muted text-center">@lang('No data')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if ($jobs->hasPages())
        {{ paginateLinks($jobs) }}
    @endif
@endsection
