<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Lead CRM') }}</title>
    @if (! app()->environment('testing') && (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="crm-shell">
    <div class="mx-auto flex min-h-screen max-w-[1700px] gap-6 px-4 py-6 lg:px-6">
        <aside class="crm-card hidden w-80 shrink-0 p-6 lg:block">
            <div class="mb-10">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-teal-600">Lead CRM</p>
                <h1 class="mt-3 text-2xl font-black text-slate-900">Admin Control Center</h1>
                <p class="mt-2 text-sm text-slate-500">Manage intake, client workspaces, website health, and delivery monitoring from one place.</p>
            </div>

            <nav class="space-y-2">
                <a href="{{ route('dashboard') }}" class="crm-sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                <a href="{{ route('admin.monitoring.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">Monitoring</a>
                <a href="{{ route('admin.clients.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">Clients</a>
                <a href="{{ route('admin.websites.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.websites.*') ? 'active' : '' }}">Websites</a>
                <a href="{{ route('admin.forms.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.forms.*') ? 'active' : '' }}">Forms</a>
                <a href="{{ route('admin.leads.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.leads.*') ? 'active' : '' }}">Leads</a>
                <a href="{{ route('admin.email-logs.index') }}" class="crm-sidebar-link {{ request()->routeIs('admin.email-logs.*') ? 'active' : '' }}">Email Logs</a>
            </nav>
        </aside>

        <main class="min-w-0 flex-1">
            <div class="crm-card mb-6 flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">{{ now()->format('d M Y') }}</p>
                    <h2 class="text-2xl font-black text-slate-900">{{ $heading ?? 'Lead Management' }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600">{{ auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="crm-button-secondary">Logout</button>
                    </form>
                </div>
            </div>

            @if (session('status'))
                <div class="crm-card mb-6 border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="crm-card mb-6 border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</body>
</html>
