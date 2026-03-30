@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="profile-main-section">
        <div class="container-fluid px-0">
            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="profile-bio">
                        <div class="profile-bio__item">
                            @include('Template::user.profile.top')
                            <form action="{{ route('user.store.profile.skill') }}" method="POST">
                                @csrf

                                <div class="student-profile-section pb-4 mb-4 border-bottom border-light">
                                    <h5 class="mb-3">@lang('Skills') <span class="text--danger">*</span></h5>
                                    <p class="text-muted small mb-3">@lang('Select your core skills. Add custom tags with comma or Enter.')</p>
                                    <div class="form-group">
                                        <select class="form-select form--control select2-skills" name="skills[]" multiple="multiple" required>
                                            @foreach ($skills as $skill)
                                                <option value="{{ $skill->name }}" @selected(isset($selectedSkillKeys[mb_strtolower($skill->name)]))>
                                                    {{ __($skill->name) }}
                                                </option>
                                            @endforeach
                                            @foreach ($selectedSkills ?? [] as $tag)
                                                @if (! $skills->contains(fn ($s) => mb_strtolower((string) $s->name) === mb_strtolower((string) $tag)))
                                                    <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="student-profile-section pb-4 mb-4 border-bottom border-light">
                                    <h5 class="mb-3">@lang('Experience')</h5>
                                    <div class="row gy-3">
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Years of relevant experience')</label>
                                            <input type="text" name="experience_years" class="form-control form--control"
                                                value="{{ old('experience_years', $studentProfile->experience_years ?? '') }}"
                                                placeholder="@lang('e.g. 1–2 years')">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form--label">@lang('Expertise level')</label>
                                            <select name="expertise_level" class="form-select form--control">
                                                <option value="">@lang('Select')</option>
                                                @foreach ($expertiseLevels as $key => $label)
                                                    <option value="{{ $key }}" @selected(old('expertise_level', $studentProfile->expertise_level ?? '') == $key)>
                                                        {{ __($label) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form--label">@lang('Experience details')</label>
                                            <textarea name="training_experience" rows="6" class="form-control form--control"
                                                placeholder="@lang('Articleship, internships, projects, tools…')">{{ old('training_experience', $studentProfile->training_experience ?? '') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-wrapper">
                                    <a href="{{ route('user.profile.setting') }}" class="btn btn-outline--dark">
                                        <i class="las la-angle-double-left"></i> @lang('Previous')
                                    </a>
                                    <button type="submit" class="btn btn--dark">@lang('Next: Portfolio') <i class="las la-angle-double-right"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    @include('Template::user.profile.info')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush
@push('style-lib')
    <link href="{{ asset('assets/global/css/select2.min.css') }}" rel="stylesheet">
@endpush
@push('script')
    <script>
        (function($) {
            'use strict';
            $('.select2-skills').each(function() {
                $(this).wrap('<div class="position-relative"></div>').select2({
                    tags: true,
                    tokenSeparators: [','],
                    maximumSelectionLength: 25,
                    placeholder: @json(__('Select or type skills, separate with comma')),
                    dropdownParent: $(this).parent()
                });
            });
        })(jQuery);
    </script>
@endpush
@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
