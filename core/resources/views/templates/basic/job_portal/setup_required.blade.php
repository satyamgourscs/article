@extends('Template::layouts.frontend')
@section('content')
    <section class="account py-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card custom--card border-warning">
                        <div class="card-body p-4">
                            <h3 class="mb-3">{{ __($pageTitle ?? 'Job portal setup') }}</h3>
                            <p class="mb-3">
                                @lang('The job portal database tables are not installed yet. An administrator must run Laravel migrations on the server.')
                            </p>
                            <ol class="text-muted small mb-4">
                                <li>@lang('Open a terminal in the project `core` directory (where `artisan` lives).')</li>
                                <li>@lang('Confirm `.env` has the correct MySQL `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`.')</li>
                                <li><code class="user-select-all">php artisan migrate --force</code></li>
                            </ol>
                            <p class="mb-0">
                                <a href="{{ route('home') }}" class="btn btn--base btn--sm">@lang('Back to home')</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
