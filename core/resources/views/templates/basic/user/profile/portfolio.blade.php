@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-main-section">
        <div class="container-fluid px-0">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="profile-bio">
                        <div class="profile-bio__item">
                            @include('Template::user.profile.top')

                            <button type="button" class="btn btn--base mb-2 ms-auto d-block portfolioModalBtn">
                                <i class="las la-plus-circle"></i> @lang('Add Portfolio')
                            </button>

                           <div class="dashboard-table">
                            <table class="table table--responsive--md mt-4">
                                <thead>
                                    <tr>
                                        <th> @lang('Image') </th>
                                        <th> @lang('Title') </th>
                                        <th> @lang('Role') </th>
                                        <th> @lang('Status') </th>
                                        <th> @lang('Action') </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($portfolios as $portfolio)
                                        @php
                                            $portfolio->image_with_path = getImage(
                                                getFilePath('portfolio') . '/' . $portfolio->image,
                                                getFileSize('portfolio'),
                                            );
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="avatar avatar--sm">
                                                    <img src="{{ getImage(getFilePath('portfolio') . '/' . $portfolio->image, avatar: true) }}"
                                                        alt="Image">
                                                </div>
                                            </td>
                                            <td><span class="clamping"> {{ __($portfolio->title) }} </span></td>
                                            <td><span class="clamping"> {{ __(@$portfolio->role) }} </span></td>
                                            <td>
                                                @php echo $portfolio->statusBadge @endphp
                                            </td>
                                            <td>
                                                <div class="action-btn">
                                                    <button class="action-btn__icon">
                                                        <i class="fa-solid fa-caret-down"></i>
                                                    </button>
                                                    <ul class="action-dropdown">
                                                        <li class="action-dropdown__item portfolioModalBtn"
                                                            data-modal_title="@lang('Update Portfolio')"
                                                            data-resource='@json($portfolio)'><a
                                                                class="action-dropdown__link" href="javascript:void(0)">
                                                                <span class="text">@lang('Edit')</span>
                                                            </a></li>
                                                        <li class="action-dropdown__item">
                                                            @if ($portfolio->status)
                                                                <a class="action-dropdown__link  portfolioEDBtn"
                                                                    href="javascript:void(0)"
                                                                    data-question="@lang('Are you sure to disable this portfolio?')"
                                                                    data-action="{{ route('user.status.profile.portfolio', $portfolio->id) }}">
                                                                    <span class="text">@lang('Disable') </span>
                                                                </a>
                                                            @else
                                                                <a class="action-dropdown__link  portfolioEDBtn"
                                                                    href="javascript:void(0)"
                                                                    data-question="@lang('Are you sure to Enable this portfolio?')"
                                                                    data-action="{{ route('user.status.profile.portfolio', $portfolio->id) }}">
                                                                    <span class="text">@lang('Enable') </span>
                                                                </a>
                                                            @endif
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center msg-center">
                                                @include('Template::partials.empty', [
                                                    'message' => 'Portfolio not found!',
                                                ])
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                           </div>
                            @if ($portfolios->hasPages())
                                <div class="mt-2">
                                    {{ paginateLinks($portfolios) }}
                                </div>
                            @endif

                        </div>
                    </div>
                    <div class="btn-wrapper">
                        <a href="{{ route('user.profile.skill') }}" class="btn btn-outline--dark">
                            <i class="las la-angle-double-left"></i> @lang('Previous')
                        </a>
                        <a href="{{ route('user.profile.education') }}" class="btn btn--base">
                            @lang('Next: Education (optional)') <i class="las la-angle-double-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <!--================== sidebar start here ================== -->
                    @include('Template::user.profile.info')
                    <!--================== sidebar end here ==================== -->
                </div>
            </div>
        </div>
    </div>


    <div class="modal custom--modal" id="portfolioModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('user.store.profile.portfolio') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="portfolio_id" value="0">
                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-2 modal-title"></h5>
                            <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Project Title')</label>
                            <input class="form-control form--control" name="title" type="text" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Your role (optional)')</label>
                            <input class="form-control form--control" name="role" type="text">
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Project description')</label>
                            <textarea class="form-control form--control" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">@lang('Project link (optional)')</label>
                            <input class="form-control form--control" name="project_url" type="url"
                                placeholder="https://">
                        </div>
                        <div class="form-group">
                            <label class="form--label"> @lang('Project Cover Image') </label>
                            <x-image-uploader :imagePath="getImage(null, getFileSize('portfolio'))" :size="getFileSize('portfolio')" class="w-100" id="imageEdit"
                                :required="false" />
                        </div>
                        <div class="text-end">
                            <button class="btn btn--base" type="submit">@lang('Submit')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="portfolioEDModal" class="modal custom--modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="question"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--danger" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--base">@lang('Yes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            // Open Portfolio Modal and Populate Data
            let portfolioModal = $("#portfolioModal");
            let form = portfolioModal.find("form");
            const action = form[0] ? form[0].action : null;

            $(".portfolioModalBtn").on('click', function() {

                let data = $(this).data();
                let resource = data.resource ?? null;

                if (!resource) {
                    form[0].reset();
                    form[0].action = `${action}`;
                    portfolioModal.find(".modal-title").text(`@lang('Add New Project')`);
                    portfolioModal.find('.image-upload-preview').css('background-image', '').removeClass('has-image');
                }
                if (resource) {
                    portfolioModal.find(".modal-title").text(`${data.modal_title}`);
                    form[0].action = `${action}/${resource.id}`;

                    if (resource.image_with_path) {
                        let preview = portfolioModal.find('.image-upload-wrapper .image-upload-preview');
                        $(preview).css('background-image', `url(${resource.image_with_path})`);
                        $(preview).addClass('has-image');
                    }

                    portfolioModal.find("[name='title']").val(resource.title);
                    portfolioModal.find("[name='role']").val(resource.role);
                    portfolioModal.find("[name='description']").val(resource.description);
                    portfolioModal.find("[name='project_url']").val(resource.url || '');
                }

                portfolioModal.modal("show");
            });

            // Delete Modal
            $('.portfolioEDBtn').on('click',function() {
                var modal = $('#portfolioEDModal');
                let data = $(this).data();
                modal.find('.question').text(`${data.question}`);
                modal.find('form').attr('action', `${data.action}`);
                modal.modal('show');
            });


        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .profile-bio .pagination {
            border: 1px solid hsl(var(--black)/.1);
        }

        .profile-bio .table tbody tr:last-child td:first-child {
            border-radius: 0;
        }

        .profile-bio .table tbody tr:last-child td:last-child {
            border-radius: 0;
        }
    </style>
@endpush
