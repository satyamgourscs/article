@extends('Template::layouts.buyer_master')
@section('content')
    @php
        $isEdit = isset($job) && $job;
    @endphp
    <form method="POST"
        action="{{ $isEdit ? route('firm.posted_jobs.update', $job) : route('firm.post_job.store') }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif
        <div class="row gy-3">
            <div class="col-12">
                <label class="form--label">@lang('Job type')</label>
                <select name="type" id="postedJobType" class="form-control form--control" required>
                    <option value="">@lang('Select')</option>
                    <option value="{{ \App\Models\PostedJob::TYPE_ARTICLESHIP }}"
                        @selected(old('type', $job->type ?? '') === \App\Models\PostedJob::TYPE_ARTICLESHIP)>@lang('Articleship')
                    </option>
                    <option value="{{ \App\Models\PostedJob::TYPE_SHORT_TERM }}"
                        @selected(old('type', $job->type ?? '') === \App\Models\PostedJob::TYPE_SHORT_TERM)>@lang('Short term audit')
                    </option>
                </select>
            </div>
            <div class="col-12">
                <label class="form--label">@lang('Title')</label>
                <input type="text" name="title" class="form-control form--control" required
                    value="{{ old('title', $job->title ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form--label">@lang('Domain')</label>
                <input type="text" name="domain" class="form-control form--control"
                    value="{{ old('domain', $job->domain ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form--label">@lang('Location')</label>
                <input type="text" name="location" class="form-control form--control"
                    value="{{ old('location', $job->location ?? '') }}">
            </div>

            <div class="col-12 field-articleship">
                <label class="form--label">@lang('Company name')</label>
                <input type="text" name="company_name" class="form-control form--control"
                    value="{{ old('company_name', $job->company_name ?? '') }}">
            </div>
            <div class="col-md-6 field-articleship">
                <label class="form--label">@lang('Stipend')</label>
                <input type="number" step="0.01" name="stipend" class="form-control form--control"
                    value="{{ old('stipend', $job->stipend ?? '') }}">
            </div>
            <div class="col-md-6 field-articleship">
                <label class="form--label">@lang('Open positions')</label>
                <input type="number" name="open_positions" class="form-control form--control"
                    value="{{ old('open_positions', $job->open_positions ?? '') }}">
            </div>
            <div class="col-md-6 field-articleship">
                <label class="form--label">@lang('Current articles')</label>
                <input type="number" name="current_articles" class="form-control form--control"
                    value="{{ old('current_articles', $job->current_articles ?? '') }}">
            </div>

            <div class="col-md-6 field-short d-none">
                <label class="form--label">@lang('Per day pay')</label>
                <input type="number" step="0.01" name="per_day_pay" class="form-control form--control"
                    value="{{ old('per_day_pay', $job->per_day_pay ?? '') }}">
            </div>
            <div class="col-md-6 field-short d-none">
                <label class="form--label">@lang('Duration')</label>
                <input type="text" name="duration" class="form-control form--control"
                    value="{{ old('duration', $job->duration ?? '') }}">
            </div>

            <div class="col-12">
                <label class="form--label">@lang('Description')</label>
                <textarea name="description" class="form-control form--control" rows="4">{{ old('description', $job->description ?? '') }}</textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn--base">@lang('Save')</button>
                <a href="{{ route('firm.posted_jobs.index') }}" class="btn btn-outline--dark">@lang('Cancel')</a>
            </div>
        </div>
    </form>
@endsection

@push('script')
    <script>
        (function() {
            const typeEl = document.getElementById('postedJobType');
            const article = document.querySelectorAll('.field-articleship');
            const short = document.querySelectorAll('.field-short');

            function toggle() {
                const v = typeEl.value;
                const art = v === @json(\App\Models\PostedJob::TYPE_ARTICLESHIP);
                article.forEach(e => {
                    e.classList.toggle('d-none', !art);
                    e.querySelectorAll('input,select,textarea').forEach(i => i.disabled = !art);
                });
                short.forEach(e => {
                    e.classList.toggle('d-none', art);
                    e.querySelectorAll('input,select,textarea').forEach(i => i.disabled = art);
                });
            }
            typeEl.addEventListener('change', toggle);
            toggle();
        })();
    </script>
@endpush
