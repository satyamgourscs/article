@extends('Template::layouts.buyer_master')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <div class="d-flex flex-wrap gap-2">
            @if ($postedJob->status === \App\Models\PostedJob::STATUS_OPEN)
                <form action="{{ route('firm.posted_jobs.filled', $postedJob) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn--sm btn--base">@lang('Mark as filled')</button>
                </form>
            @endif
            <a href="{{ route('firm.posted_jobs.edit', $postedJob) }}" class="btn btn--sm btn-outline--base">@lang('Edit')</a>
        </div>
    </div>
    <p><strong>@lang('Status'):</strong> {{ __($postedJob->status) }}</p>
    <p><strong>@lang('Type'):</strong> {{ $postedJob->type }}</p>
    @if ($postedJob->description)
        <div class="mb-4">{!! nl2br(e($postedJob->description)) !!}</div>
    @endif

    <h6 class="mb-2">@lang('Applications')</h6>
    <div class="table-responsive">
        <table class="table table--light">
            <thead>
                <tr>
                    <th>@lang('Student')</th>
                    <th>@lang('Applied')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($postedJob->applications as $application)
                    <tr>
                        <td>{{ $application->user->fullname ?? $application->user->username }}</td>
                        <td>{{ showDateTime($application->applied_at) }}</td>
                        <td>{{ __($application->status) }}</td>
                        <td>
                            <form method="POST"
                                action="{{ route('firm.posted_jobs.application.status', [$postedJob, $application]) }}"
                                class="d-flex flex-wrap gap-1 align-items-center">
                                @csrf
                                <select name="status" class="form-control form--control form--sm">
                                    <option value="{{ \App\Models\JobApplication::STATUS_APPLIED }}" @selected($application->status === \App\Models\JobApplication::STATUS_APPLIED)>@lang('Applied')</option>
                                    <option value="{{ \App\Models\JobApplication::STATUS_SELECTED }}" @selected($application->status === \App\Models\JobApplication::STATUS_SELECTED)>@lang('Selected')</option>
                                    <option value="{{ \App\Models\JobApplication::STATUS_REJECTED }}" @selected($application->status === \App\Models\JobApplication::STATUS_REJECTED)>@lang('Rejected')</option>
                                </select>
                                <button type="submit" class="btn btn--sm btn-outline--base">@lang('Update')</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-muted">@lang('No applications yet.')</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <a href="{{ route('firm.posted_jobs.index') }}">← @lang('Back')</a>
@endsection
