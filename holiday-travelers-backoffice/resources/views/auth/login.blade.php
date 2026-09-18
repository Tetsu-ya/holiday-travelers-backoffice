<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · Holiday Travelers Inc.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#0b1f3a] px-4 py-8 font-body">
    <main class="grid w-full max-w-4xl overflow-hidden rounded-[2rem] border border-border bg-card shadow-2xl shadow-primary/10 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-primary via-[#12345f] to-[#0d2749] p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-16 -top-16 h-48 w-48 rounded-full border-[24px] border-accent/15"></div>
            <div class="relative">
                <div class="mb-8 flex h-14 w-14 items-center justify-center bg-white p-1 shadow-lg shadow-black/10">
                    <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-accent">Holiday Travelers Inc.</p>
                <h2 class="mt-4 max-w-xs font-heading text-3xl font-semibold leading-tight">Move every journey forward.</h2>
                <p class="mt-4 max-w-xs text-sm leading-6 text-white/65">Your operations hub for bookings, partners, resources, and travel teams.</p>
            </div>
            <p class="relative text-xs text-white/40">Back Office · Secure workspace</p>
        </section>

        <section class="p-7 sm:p-10">
            <div class="mb-8 lg:hidden">
                <div class="mb-5 flex h-12 w-12 items-center justify-center bg-white p-1 shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Holiday Travelers Inc.</p>
            </div>
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-secondary">Welcome back</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-primary">Sign in to your workspace</h1>
                <p class="mt-2 text-sm text-gray-500">Use your staff credentials to continue.</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-error/30 bg-error/10 px-4 py-3 text-sm text-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-semibold text-primary">Email address</label>
                    <input type="email" name="email" required autofocus autocomplete="email"
                           class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-primary outline-none transition placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/15">
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between"><label class="block text-sm font-semibold text-primary">Password</label><span class="text-xs text-gray-400">Protected access</span></div>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full rounded-xl border border-border bg-background px-4 py-3 text-sm text-primary outline-none transition placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/15">
                </div>
                <button type="submit" class="w-full rounded-xl bg-secondary px-4 py-3 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/20 transition hover:-translate-y-0.5 hover:opacity-90">
                    Continue to back office
                </button>
            </form>
            <p class="mt-8 text-center text-xs text-gray-400">A verification code will be sent after sign in.</p>
        </section>
    </main>
</body>
</html>
