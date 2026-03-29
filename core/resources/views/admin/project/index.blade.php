@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Opportunity Title')</th>
                                    <th>@lang('Student')</th>
                                    <th>@lang('Firm')</th>
                                    <th>@lang('Estimate Time')</th>
                                    <th>@lang('Bid Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                    @php
                                        $conversation = App\Models\Conversation::where([
                                            ['user_id', $project->user_id],
                                            ['buyer_id', $project->buyer_id],
                                        ])->first();

                                        $bid = $project->bid;
                                    @endphp
                                    <tr>
                                        <td>{{ isset($project->job) && $project->job ? strLimit(__($project->job->title), 50) : __('N/A') }}</td>
                                        <td>
                                            @if(isset($project->user) && $project->user)
                                                <div>
                                                    <span class="fw-bold">{{ $project->user->fullname }}</span>
                                                    <br>
                                                    <small>
                                                        <a href="{{ route('admin.users.detail', $project->user->id) }}"><span>@</span>{{ $project->user->username }}</a>
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text--muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($project->buyer) && $project->buyer)
                                                <div>
                                                    <span class="fw-bold">{{ $project->buyer->fullname }}</span>
                                                    <br>
                                                    <small>
                                                        <a href="{{ route('admin.buyers.detail', $project->buyer->id) }}"><span>@</span>{{ $project->buyer->username }}</a>
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text--muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($project->bid) && $project->bid)
                                                <span class="fw-bold">
                                                    {{ $project->bid->estimated_time }}
                                                </span>
                                            @else
                                                <span class="text--muted">@lang('N/A')</span>
                                            @endif
                                        </td>
                                        
                                        <td>{{ isset($project->bid) && $project->bid ? showAmount($project->bid->bid_amount) : __('N/A') }}</td>
                                        <td>@php echo $project->statusBadge; @endphp</td>
                                        <td>
                                            <div class="btn--group">
                                                <div class="d-flex justify-content-end flex-wrap gap-1">
                                                    @if ($project->status == Status::PROJECT_REPORTED)
                                                        <button class="btn btn-outline--info btn-sm dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            <i class="las la-ellipsis-v"></i> @lang('More')
                                                        </button>
                                                        <ul class="dropdown-menu px-2">
                                                            <li>
                                                                <a href="{{ route('admin.project.details', $project->id) }}"
                                                                    class="dropdown-item cursor-pointer text--primary">
                                                                    <i class="la la-desktop"></i> @lang('Details')
                                                                </a>
                                                            </li>
                                                            @if(isset($conversation) && $conversation)
                                                                <li>
                                                                    <a href="{{ route('admin.project.conversation', ['id' => $conversation->id, 'projectId' => $project->id]) }}"
                                                                        class="dropdown-item cursor-pointer text--warning">
                                                                         <i class="lab la-rocketchat"></i> @lang('Chat')
                                                                     </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    @else
                                                        <a class="btn btn-sm btn-outline--primary"
                                                            href="{{ route('admin.project.details', $project->id) }}">
                                                            <i class="las la-desktop"></i> @lang('Details')
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage ?? 'No projects found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($projects->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($projects) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-search-form dateSearch="yes" placeholder="Title/Username" />
@endpush
