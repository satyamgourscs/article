@extends('Template::layouts.master')
@section('content')
    <div class="dashboard-body">
        <div class="table-responsive">
            <table class="table table--light">
                <thead>
                    <tr>
                        <th>@lang('Job')</th>
                        <th>@lang('Firm')</th>
                        <th>@lang('Applied')</th>
                        <th>@lang('Status')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        <tr>
                            <td>
                                <a href="{{ route('jobs.portal.show', $application->postedJob) }}">{{ __($application->postedJob->title) }}</a>
                            </td>
                            <td>{{ '@'.($application->postedJob->buyer->username ?? '') }}</td>
                            <td>{{ showDateTime($application->applied_at) }}</td>
                            <td>{{ __($application->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted">@lang('No applications yet.')</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($applications->hasPages())
            {{ paginateLinks($applications) }}
        @endif
    </div>
@endsection

