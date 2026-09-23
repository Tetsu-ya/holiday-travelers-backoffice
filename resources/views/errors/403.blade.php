<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access restricted · Holiday Travelers Inc.</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-[#eef5fb] font-body text-primary">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden p-6">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-accent/20 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-20 h-96 w-96 rounded-full bg-secondary/15 blur-3xl"></div>
        <section class="relative w-full max-w-xl overflow-hidden rounded-[2rem] border border-white/80 bg-white/80 shadow-2xl shadow-primary/15 backdrop-blur-xl">
            <div class="relative bg-gradient-to-br from-primary via-[#164477] to-secondary px-8 py-10 text-center text-white sm:px-12">
                <div class="absolute right-6 top-6 h-20 w-20 rounded-full border border-white/10 bg-white/10"></div>
                <div class="absolute -bottom-10 -left-8 h-28 w-28 rounded-full border border-white/10 bg-white/5"></div>
                <div class="relative mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-white p-1.5 shadow-lg shadow-black/20">
                    <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                </div>
                <p class="relative mt-6 text-[11px] font-semibold uppercase tracking-[0.24em] text-white/60">Holiday Travelers · Back Office</p>
                <p class="relative mt-3 text-6xl font-heading font-bold tracking-tight">403</p>
            </div>
            <div class="px-8 py-10 text-center sm:px-12">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-error/10 text-error ring-8 ring-error/[0.03]" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.01.01 0M10.3 4.7 2.9 17.5a1.8 1.8 0 0 0 1.56 2.7h15.08a1.8 1.8 0 0 0 1.56-2.7L13.7 4.7a1.96 1.96 0 0 0-3.4 0Z"/></svg>
                </div>
                <h1 class="mt-6 font-heading text-2xl font-semibold tracking-tight text-primary">Access restricted</h1>
                <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-gray-500">
                    You don’t have permission to view this page. If you believe you should have access, please contact your administrator.
                </p>
                <div class="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/20 transition hover:-translate-y-0.5 hover:bg-secondary">Return to dashboard</a>
                        <a href="{{ url()->previous() }}" class="rounded-xl border border-border bg-white/70 px-5 py-2.5 text-sm font-semibold text-primary transition hover:-translate-y-0.5 hover:border-secondary hover:text-secondary">Go back</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/15 transition hover:bg-secondary">Go to login</a>
                    @endauth
                </div>
            </div>
        </section>
    </main>
</body>
</html>
