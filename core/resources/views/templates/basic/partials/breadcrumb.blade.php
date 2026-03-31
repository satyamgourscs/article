@php
    $breadcrumbFilename = '';
    if (\App\Support\SafeSchema::hasColumn('general_settings', 'breadcrumb_image')) {
        $breadcrumbFilename = trim((string) (gs('breadcrumb_image') ?? ''));
    }
    $rel = $breadcrumbFilename !== '' ? getFilePath('breadcrumb').'/'.$breadcrumbFilename : '';
    $defaultJpg = public_path('assets/images/default-breadcrumb.jpg');
    if ($rel !== '' && file_exists(public_path($rel))) {
        $breadcrumbBgUrl = getImage($rel, getFileSize('breadcrumb'));
    } elseif (file_exists($defaultJpg)) {
        $breadcrumbBgUrl = asset('assets/images/default-breadcrumb.jpg');
    } else {
        $breadcrumbBgUrl = asset('assets/images/default.png');
    }
@endphp
<section class="breadcrumb breadcrumb-area"
    style="background-image: url('{{ $breadcrumbBgUrl }}');">
    <div class="breadcrumb-overlay" aria-hidden="true"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="breadcrumb__wrapper">
                    <h3 class="breadcrumb__title">{{ __(@$customPageTitle) ?? __(@$pageTitle) }}</h3>
                    <ul class="breadcrumb__list">
                        <li class="breadcrumb__item"><a href="{{ route('home') }}" class="breadcrumb__link">
                                @lang('Home')</a>
                        </li>
                        @if (@$customSubPageTitle)
                            <li class="breadcrumb__item"><i class="fas fa-angle-right"></i></li>
                            <li class="breadcrumb__item"><a href="{{ @$toRoute }}" class="breadcrumb__link">
                                    {{ __(@$customSubPageTitle) }}</a>
                            </li>
                        @endif
                        <li class="breadcrumb__item"><i class="fas fa-angle-right"></i></li>
                        <li class="breadcrumb__item"> <span class="breadcrumb__item-text">
                                {{ __(@$customPageTitle) ?? __(@$pageTitle) }}</span> </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
