<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 22px; margin: 0 0 8px; }
        h2 { font-size: 14px; margin: 16px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .muted { color: #555; font-size: 11px; }
        ul { margin: 4px 0 0 18px; padding: 0; }
        li { margin-bottom: 4px; }
        .item { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>{{ $user->fullname }}</h1>
    @if ($user->username)
        <div class="muted">{{ '@'.$user->username }}</div>
    @endif
    @if ($user->tagline)
        <p class="muted">{{ $user->tagline }}</p>
    @endif

    <h2>@lang('Contact')</h2>
    <ul>
        @if ($user->email)
            <li>{{ $user->email }}</li>
        @endif
        @if ($user->mobile)
            <li>{{ $user->mobileNumber }}</li>
        @endif
        @if ($user->city || $user->state)
            <li>{{ trim(implode(', ', array_filter([$user->city, $user->state, $user->country_name]))) }}</li>
        @endif
    </ul>

    @if ($user->about)
        <h2>@lang('Summary')</h2>
        <p>{{ strip_tags($user->about) }}</p>
    @endif

    @if ($user->skills->isNotEmpty())
        <h2>@lang('Skills')</h2>
        <p>{{ $user->skills->pluck('name')->implode(', ') }}</p>
    @endif

    @if ($user->educations->isNotEmpty())
        <h2>@lang('Education')</h2>
        @foreach ($user->educations as $ed)
            <div class="item">{{ $ed->school ?? '' }}</div>
        @endforeach
    @endif

    @if ($user->portfolios->isNotEmpty())
        <h2>@lang('Portfolio')</h2>
        @foreach ($user->portfolios as $p)
            <div class="item">
                <strong>{{ $p->title }}</strong>
                @if ($p->description)
                    <div>{{ strLimit(strip_tags($p->description), 400) }}</div>
                @endif
                @if ($p->url)
                    <div class="muted">{{ $p->url }}</div>
                @endif
            </div>
        @endforeach
    @endif
</body>
</html>
