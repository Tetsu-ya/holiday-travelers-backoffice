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
<body class="min-h-screen bg-[#0b1f3a] font-body text-primary">
    <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:p-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(111,169,230,0.35),_transparent_31rem)]"></div>
        <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.035)_1px,transparent_1px)] bg-[size:42px_42px]"></div>
        <div class="absolute -right-24 top-16 h-72 w-72 rounded-full bg-accent/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-20 h-80 w-80 rounded-full bg-[#174b83] blur-3xl"></div>
        <main class="relative grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/70 bg-card/95 shadow-2xl shadow-primary/15 backdrop-blur-sm lg:grid-cols-[1fr_1.1fr]">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-primary via-[#123f76] to-[#0b2547] p-11 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[28px] border-white/10"></div>
            <div class="absolute -bottom-24 -left-16 h-56 w-56 rounded-full bg-accent/10"></div>
            <div class="relative">
                <div class="mb-9 flex h-14 w-14 items-center justify-center rounded-2xl bg-white p-2 shadow-xl shadow-black/20">
                    <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-accent">Holiday Travelers Inc.</p>
                <h2 class="mt-5 max-w-sm font-heading text-4xl font-semibold leading-tight">Travel operations, beautifully organized.</h2>
                <p class="mt-5 max-w-sm text-sm leading-6 text-white/70">Manage bookings, partners, resources, and your travel team in one secure workspace.</p>
                <div class="mt-9 grid max-w-sm grid-cols-1 gap-3">
                    <div class="rounded-xl border border-white/10 bg-white/[0.07] p-3.5 backdrop-blur-sm">
                        <p class="text-lg font-semibold text-white">Secure</p>
                        <p class="mt-1 text-[11px] text-white/55">Verified sign-in</p>
                    </div>
                </div>
            </div>
            <p class="relative text-xs text-white/40">Back Office · Secure workspace</p>
        </section>

        <section class="p-7 sm:p-11">
            <div class="mb-9 lg:hidden">
                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-white p-2 shadow-md shadow-primary/10 ring-1 ring-primary/5">
                    <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary">Holiday Travelers Inc.</p>
            </div>
            <div class="mb-8 flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-secondary">Welcome back</p>
                <h1 class="mt-3 font-heading text-3xl font-semibold tracking-tight text-primary sm:text-4xl">Sign in to continue</h1>
                <p class="mt-3 text-sm leading-6 text-gray-500">Enter your staff credentials to access your workspace.</p>
                </div>
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/5 text-primary ring-1 ring-primary/10" title="Secure sign-in">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75 19.5 7.5v4.87c0 4.3-3.12 7.45-7.5 8.88-4.38-1.43-7.5-4.58-7.5-8.88V7.5L12 3.75Z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.5 1.5 3.5-3.5"/></svg>
                </div>
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
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="m3 7 9 6 9-6"/></svg>
                        <input type="email" name="email" required autofocus autocomplete="email" placeholder="name@company.com"
                               class="w-full rounded-xl border border-border bg-background py-3.5 pl-11 pr-4 text-sm text-primary outline-none transition placeholder:text-gray-400 focus:border-accent focus:bg-white focus:ring-4 focus:ring-accent/15">
                    </div>
                </div>
                <div>
                    <div class="mb-2 flex items-center justify-between"><label class="block text-sm font-semibold text-primary">Password</label><span class="text-xs text-gray-400">Protected access</span></div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full rounded-xl border border-border bg-background px-4 py-3.5 pr-14 text-sm text-primary outline-none transition focus:border-accent focus:bg-white focus:ring-4 focus:ring-accent/15">
                        <button id="password-toggle" type="button" aria-controls="password" aria-pressed="false" aria-label="Show password"
                                class="absolute right-3 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-md text-gray-400 transition duration-200 hover:bg-primary/5 hover:text-secondary focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
                                title="Show password">
                            <svg id="show-password-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.04 12.32a1.6 1.6 0 0 1 0-.64C3.47 7.38 7.38 4.5 12 4.5s8.53 2.88 9.96 7.18c.05.21.05.43 0 .64C20.53 16.62 16.62 19.5 12 19.5S3.47 16.62 2.04 12.32Z" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.75" />
                            </svg>
                            <svg id="hide-password-icon" class="hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="m3 3 18 18M10.58 5.4A10.5 10.5 0 0 1 12 5.25c4.62 0 8.53 2.88 9.96 7.18.05.21.05.43 0 .64a12.2 12.2 0 0 1-3.02 4.52M6.4 6.4a12.2 12.2 0 0 0-4.36 5.28c-.05.21-.05.43 0 .64C3.47 16.62 7.38 19.5 12 19.5c1.57 0 3.05-.33 4.38-.93M9.88 9.88a3 3 0 0 0 4.24 4.24" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="group w-full rounded-xl bg-gradient-to-r from-secondary to-[#ef8d39] px-4 py-3.5 font-button text-sm font-semibold text-white shadow-lg shadow-secondary/25 transition duration-200 hover:-translate-y-0.5 hover:shadow-xl hover:shadow-secondary/20 focus:outline-none focus-visible:ring-4 focus-visible:ring-secondary/30">
                    Continue to back office <span aria-hidden="true" class="ml-1">→</span>
                </button>
            </form>
            <p class="mt-8 text-center text-xs text-gray-400">A verification code will be sent after sign in.</p>
        </section>
        </main>
    </div>
    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        const showPasswordIcon = document.getElementById('show-password-icon');
        const hidePasswordIcon = document.getElementById('hide-password-icon');

        passwordToggle.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            passwordToggle.setAttribute('aria-pressed', String(isHidden));
            passwordToggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            passwordToggle.title = isHidden ? 'Hide password' : 'Show password';
            showPasswordIcon.classList.toggle('hidden', isHidden);
            hidePasswordIcon.classList.toggle('hidden', !isHidden);
        });
    </script>
</body>
</html>
