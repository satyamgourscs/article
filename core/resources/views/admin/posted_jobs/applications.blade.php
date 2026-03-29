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
                            <th>@lang('Job')</th>
                            <th>@lang('Student')</th>
                            <th>@lang('Applied')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ __($row->postedJob->title ?? '') }}</td>
                                <td>{{ $row->user->fullname ?? $row->user->username }}</td>
                                <td>{{ showDateTime($row->applied_at) }}</td>
                                <td>{{ __($row->status) }}</td>
                                <td>
                                    <form method="POST"
                                        action="{{ route('admin.posted.portal.application.status', $row) }}"
                                        class="d-flex flex-wrap gap-1 align-items-center">
                                        @csrf
                                        <select name="status" class="form-control form-control-sm">
                                            <option value="{{ \App\Models\JobApplication::STATUS_APPLIED }}" @selected($row->status === \App\Models\JobApplication::STATUS_APPLIED)>@lang('Applied')</option>
                                            <option value="{{ \App\Models\JobApplication::STATUS_SELECTED }}" @selected($row->status === \App\Models\JobApplication::STATUS_SELECTED)>@lang('Selected')</option>
                                            <option value="{{ \App\Models\JobApplication::STATUS_REJECTED }}" @selected($row->status === \App\Models\JobApplication::STATUS_REJECTED)>@lang('Rejected')</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn--primary">@lang('Save')</button>
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
    @if ($applications->hasPages())
        {{ paginateLinks($applications) }}
    @endif
@endsection
