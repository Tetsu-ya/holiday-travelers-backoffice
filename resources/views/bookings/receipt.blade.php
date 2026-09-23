<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt · {{ $payment->booking?->reference_no }}</title>
    @vite(['resources/css/app.css'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    <style>
        body.receipt-page { background-color: #dbe7f3; }
        body.receipt-page .receipt { background-color: #eaf1f7; }
        .dark body.receipt-page { background-color: #0f172a; }
        .dark body.receipt-page .receipt { background-color: #1f2937; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .receipt { box-shadow: none !important; border: 0 !important; background: white !important; }
        }
    </style>
</head>
<body class="receipt-page min-h-screen bg-[radial-gradient(circle_at_top_left,_rgba(111,169,230,0.16),_transparent_35rem),#dbe7f3] p-5 font-body text-primary transition-colors duration-300 dark:bg-[radial-gradient(circle_at_top_left,_rgba(49,86,121,0.35),_transparent_35rem),#0f172a] dark:text-gray-100 sm:p-10">
    <main class="mx-auto max-w-xl rounded-[2rem] border border-white/70 bg-white/45 p-3 shadow-2xl shadow-slate-900/10 backdrop-blur-xl sm:p-5 dark:border-gray-700/80 dark:bg-gray-900/35">
        <div class="no-print mb-5 flex items-center justify-between gap-3"><a href="{{ route('bookings.finance', $payment->booking) }}" class="group inline-flex items-center gap-2 rounded-xl border border-border bg-white/80 px-4 py-2.5 text-sm font-semibold text-primary shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-secondary hover:text-secondary hover:shadow-md active:translate-y-0 active:scale-95 dark:bg-gray-800/80 dark:text-gray-100"><span class="transition-transform duration-200 group-hover:-translate-x-1">&#8592;</span><span>Back</span></a><button onclick="window.print()" class="group inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary/20 transition-all duration-200 hover:-translate-y-0.5 hover:bg-secondary hover:shadow-xl active:translate-y-0 active:scale-95"><svg class="h-4 w-4 transition-transform duration-200 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v6H6z"/><path stroke-linecap="round" d="M18 12h.01"/></svg><span>Print receipt</span></button></div>
        <section class="receipt overflow-hidden rounded-[1.5rem] border-0 bg-slate-100/95 shadow-none backdrop-blur-xl dark:bg-gray-800/95">
            <div class="relative overflow-hidden bg-gradient-to-br from-primary via-[#315679] to-secondary px-6 py-6 text-white sm:px-7"><div class="absolute -right-12 -top-16 h-44 w-44 rounded-full border border-white/10 bg-white/10"></div><div class="absolute -bottom-20 -left-10 h-40 w-40 rounded-full border border-white/10 bg-white/5"></div><div class="relative flex items-center gap-3"><div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white p-1 shadow-lg shadow-black/10"><img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc. logo" class="h-full w-full object-contain"></div><div><p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-white/70">Holiday Travelers Inc.</p><h1 class="mt-0.5 font-heading text-2xl font-semibold tracking-tight">Payment receipt</h1><p class="mt-0.5 text-xs text-white/75">Official payment record</p></div></div></div>
            <div class="space-y-5 p-5 sm:p-6">
                <div class="flex flex-col justify-between gap-3 border-b border-border pb-5 sm:flex-row"><div><p class="text-xs uppercase tracking-[0.14em] text-gray-400">Receipt reference</p><p class="mt-1 font-semibold text-primary">{{ $payment->reference_number ?: 'Receipt-' . $payment->id }}</p></div><div class="sm:text-right"><p class="text-xs uppercase tracking-[0.14em] text-gray-400">Payment date &amp; time</p><p class="mt-1 font-medium">{{ ($payment->paid_at ?? $payment->created_at)?->timezone(config('app.timezone'))->format('M d, Y h:i A') }}</p><p class="mt-1 text-[11px] text-gray-400">{{ config('app.timezone') }}</p></div></div>
                <div class="grid gap-5 sm:grid-cols-3"><div><p class="text-xs text-gray-400">Customer</p><p class="mt-1 font-semibold">{{ $payment->booking?->customer_name ?? 'N/A' }}</p><p class="text-sm text-gray-500">{{ $payment->booking?->customer_email ?? 'No email provided' }}</p></div><div><p class="text-xs text-gray-400">Booking</p><p class="mt-1 font-semibold">{{ $payment->booking?->reference_no ?? 'N/A' }}</p><p class="text-sm text-gray-500">{{ $payment->booking?->tourPackage?->name ?? 'Package unavailable' }}</p></div><div><p class="text-xs text-gray-400">Passengers</p><p class="mt-1 font-semibold">{{ $payment->booking?->pax ?? 0 }} passenger{{ ($payment->booking?->pax ?? 0) == 1 ? '' : 's' }}</p></div></div>
                <div class="rounded-xl bg-background p-5"><div class="flex items-center justify-between"><span class="text-gray-500">Payment method</span><span class="font-medium capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</span></div><div class="mt-3 flex items-center justify-between"><span class="text-gray-500">Status</span><span class="rounded-full bg-success/10 px-2.5 py-1 text-xs font-semibold capitalize text-success">{{ $payment->status }}</span></div><div class="mt-5 flex items-end justify-between border-t border-border pt-4"><span class="font-semibold">Amount paid</span><span class="font-heading text-2xl font-semibold text-primary">₱{{ number_format((float) $payment->amount, 2) }}</span></div></div>
                <p class="text-center text-xs text-gray-400">Thank you for choosing Holiday Travelers Inc.</p>
            </div>
        </section>
    </main>
</body>
</html>
