<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @if (! app()->environment('testing') && (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="crm-shell flex min-h-screen items-center justify-center px-4">
    <div class="grid w-full max-w-5xl gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="crm-card hidden p-10 lg:block">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-teal-600">Unified Lead Intake</p>
            <h1 class="mt-6 max-w-xl text-5xl font-black leading-tight text-slate-900">Monitor every client form submission from one Laravel CRM.</h1>
            <p class="mt-6 max-w-2xl text-lg text-slate-600">Track websites, capture raw form payloads, retry failed emails, and keep your lead pipeline visible across every project.</p>
            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl bg-slate-900 p-5 text-white">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-300">API</p>
                    <p class="mt-2 text-2xl font-black">Secure Intake</p>
                </div>
                <div class="rounded-3xl bg-teal-600 p-5 text-white">
                    <p class="text-sm uppercase tracking-[0.2em] text-teal-100">Queue</p>
                    <p class="mt-2 text-2xl font-black">Mail Delivery</p>
                </div>
                <div class="rounded-3xl bg-white p-5 text-slate-900 shadow-inner ring-1 ring-slate-100">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400">CRM</p>
                    <p class="mt-2 text-2xl font-black">Lead Review</p>
                </div>
            </div>
        </section>

        <section class="crm-card p-8 sm:p-10">
            <p class="text-xs font-bold uppercase tracking-[0.35em] text-slate-400">Admin Login</p>
            <h2 class="mt-4 text-3xl font-black text-slate-900">Sign in to dashboard</h2>
            <p class="mt-2 text-sm text-slate-500">Use your admin account to manage leads, websites, forms, and email health.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="crm-input" required>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-600">Password</label>
                    <input type="password" name="password" class="crm-input" required>
                </div>
                <label class="flex items-center gap-3 text-sm text-slate-500">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    <span>Keep me signed in</span>
                </label>
                <button type="submit" class="crm-button w-full">Login</button>
            </form>
        </section>
    </div>
</body>
</html>
