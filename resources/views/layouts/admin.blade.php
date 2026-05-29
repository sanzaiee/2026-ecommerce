<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — {{ $site['siteName'] ?? config('app.name') }} CMS</title>
    @if (!empty($site['faviconUrl']))
        <link rel="icon" href="{{ $site['faviconUrl'] }}">
    @endif
    @include('partials.admin.theme-boot')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @include('partials.admin.theme-vars')
    @stack('styles')
</head>

<body class="admin-body">
    @auth
        <div class="admin-shell">
            @include('partials.admin.sidebar')

            <div class="admin-shell__main">
                @include('partials.admin.header')

                <div class="admin-main">
                    @if (session('status'))
                        <div class="alert alert-success admin-flash">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger admin-flash">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    @else
        <main class="admin-main admin-main--guest py-4">
            @yield('content')
        </main>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @auth
        <script src="{{ asset('js/admin-theme.js') }}" defer></script>
        <script src="{{ asset('js/admin-shell.js') }}" defer></script>
        <script src="{{ asset('js/admin-media.js') }}" defer></script>
    @endauth
    @stack('scripts')
</body>

</html>
