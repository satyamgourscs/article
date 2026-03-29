@extends('Template::layouts.buyer_master')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <a href="{{ route('firm.post_job') }}" class="btn btn--base btn--sm">@lang('Post job')</a>
    </div>
    <div class="table-responsive">
        <table class="table table--light">
            <thead>
                <tr>
                    <th>@lang('Title')</th>
                    <th>@lang('Type')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jobs as $job)
                    <tr>
                        <td>{{ __($job->title) }}</td>
                        <td>{{ $job->type }}</td>
                        <td>{{ __($job->status) }}</td>
                        <td class="d-flex flex-wrap gap-1">
                            <a href="{{ route('firm.posted_jobs.show', $job) }}"
                                class="btn btn--sm btn-outline--base">@lang('View')</a>
                            <a href="{{ route('firm.posted_jobs.edit', $job) }}"
                                class="btn btn--sm btn-outline--primary">@lang('Edit')</a>
                            <form action="{{ route('firm.posted_jobs.destroy', $job) }}" method="POST" class="d-inline"
                                onsubmit="return confirm(@json(__('Delete this job?')))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--sm btn-outline--danger">@lang('Delete')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">@lang('No jobs yet.')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($jobs->hasPages())
        {{ paginateLinks($jobs) }}
    @endif
@endsection
