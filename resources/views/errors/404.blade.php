<!DOCTYPE html>
<html dir="ltr" lang="en-US">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Page Not Found</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" rel="stylesheet" />
    <style>
        body { font-family: Inter, system-ui, sans-serif; margin: 0; padding: 2rem; background: #fafafa; color: #333; }
        .container { max-width: 640px; margin: 0 auto; text-align: center; }
        .error-icon { font-size: 60px; margin-bottom: 0.5rem; }
        h1 { font-size: 1.75rem; font-weight: 600; margin: 1rem 0; }
        .lead { line-height: 1.6; color: #555; }
        a.button {
            display: inline-block; margin-top: 1rem; padding: 0.65rem 1.25rem;
            background: #f5f6f7; border: 1px solid #ddd; border-radius: 6px;
            color: #222; text-decoration: none; box-shadow: 0 5px 10px rgba(44,44,44,0.08);
        }
        a.button:hover { background: #eee; }
        .muted { font-size: 0.875rem; color: #888; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <section class="error-section">
        <div class="container">
            <div class="error-icon">
                <span class="material-symbols-outlined">sentiment_dissatisfied</span>
            </div>
            <h1>Oh snap! We are lost</h1>
            <p class="lead">It seems we can not find what you are looking for. Perhaps searching can help or go back to</p>
            <p>
                @php
                    $homeUrl = config('app.public_url') ?: config('app.url');
                    if (! is_string($homeUrl) || $homeUrl === '') {
                        $homeUrl = url('/');
                    }
                @endphp
                <a class="button" href="{{ $homeUrl }}">Home</a>
            </p>
            @if (config('app.debug'))
                <p class="muted">Requested path: {{ request()->getPathInfo() }}</p>
            @endif
        </div>
    </section>
</body>
</html>
