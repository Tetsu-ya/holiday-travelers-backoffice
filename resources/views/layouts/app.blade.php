<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') · Holiday Travelers Inc.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background font-body text-primary antialiased">
    <div class="flex h-screen min-h-screen overflow-hidden bg-background">
        {{-- Sidebar --}}
        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-primary/40 backdrop-blur-sm lg:hidden"></div>
        <aside id="app-sidebar" class="fixed inset-y-0 left-0 z-40 flex h-[100dvh] min-h-screen w-80 shrink-0 -translate-x-full flex-col overflow-hidden border-r border-white/10 bg-gradient-to-b from-primary via-[#12345f] to-[#0d2749] text-white shadow-2xl shadow-primary/20 transition-transform duration-200 lg:sticky lg:top-0 lg:translate-x-0">
            <div class="border-b border-white/10 bg-white/[0.03] px-5 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-white p-1 shadow-lg shadow-black/10">
                        <img src="{{ asset('images/logo.png') }}" alt="Holiday Travelers Inc." class="h-full w-full object-contain">
                    </div>
                <div class="leading-tight">
                        <p class="font-heading text-sm font-semibold tracking-tight">Holiday Travelers</p>
                        <p class="mt-0.5 text-[11px] font-medium uppercase tracking-[0.18em] text-accent">Back Office</p>
                    </div>
                </div>
            </div>
            <div class="px-4 pt-4">
                <div class="rounded-2xl border border-white/10 bg-black/10 p-3.5 shadow-inner shadow-black/10 backdrop-blur-sm">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="text-[10px] font-semibold uppercase tracking-[0.2em] text-white/45">Workspace</span>
                        <span class="flex items-center gap-1.5 rounded-full bg-success/15 px-2 py-1 text-[10px] font-semibold text-green-300">
                            <span class="h-1.5 w-1.5 rounded-full bg-green-300 shadow-[0_0_0_3px_rgba(134,239,172,0.12)]"></span>
                            Live
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-accent to-secondary text-sm font-bold text-white shadow-lg shadow-accent/30">HT</div>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">Holiday Travelers</p>
                            <p class="truncate text-[11px] text-white/60">Operations hub</p>
                        </div>
                    </div>
                </div>
            </div>
            <nav aria-label="Primary navigation" class="sidebar-scrollbar flex-1 overflow-y-auto px-4 py-5 text-sm">
                <div class="mb-3 px-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-white/40">Navigation</div>
                @php
                    $nav = [
                        [
                            'label' => 'Dashboard',
                            'route' => 'dashboard',
                            'icon' => '⌂',
                            'children' => [],
                        ],
                        [
                            'label' => 'Travel Agent & Staff Management',
                            'icon' => '👥',
                            'children' => [
                                ['Staff Profiles', 'staff-profiles.index'],
                                ['Agent Profiles', 'agent-profiles.index'],
                                ['Roles & Permissions', 'roles-permissions.index'],
                                ['Tasks', 'tasks.index'],
                                ['Scheduling', 'scheduling.index'],
                                ['Performance', 'performance.index'],
                            ],
                        ],
                        [
                            'label' => 'Supplier & Partner Management',
                            'icon' => '🤝',
                            'children' => [
                                ['Suppliers', 'suppliers.index'],
                                ['Business Partners', 'partners.index'],
                                ['Contracts', 'supplier-contracts.index'],
                                ['Rates', 'supplier-rates.index'],
                                ['Availability', 'supplier-availability.index'],
                                ['Performance', 'supplier-performance.index'],
                            ],
                        ],
                        [
                            'label' => 'Tour Availability & Resource Planning',
                            'icon' => '🗺️',
                            'children' => [
                                ['Tour Packages', 'packages.index'],
                                ['Bookings', 'bookings.index'],
                                ['Tour Schedule', 'tour-schedule.index'],
                                ['Availability', 'tour-availability.index'],
                                ['Resource Allocation', 'resource-allocation.index'],
                                ['Staff Assignment', 'staff-assignment.index'],
                                ['Resource Calendar', 'resource-calendar.index'],
                                ['AI Resource Planning', 'ai-planning.index'],
                                ['Payment Methods', 'payment-methods.index'],
                            ],
                        ],
                        [
                            'label' => 'Marketing & Promotions',
                            'icon' => '📣',
                            'children' => [
                                ['Campaigns', 'campaigns.index'],
                                ['Promotions', 'promotions.index'],
                                ['Discount Codes', 'discount-codes.index'],
                                ['Marketing Calendar', 'marketing-calendar.index'],
                                ['Campaign Analytics', 'campaign-analytics.index'],
                            ],
                        ],
                        [
                            'label' => 'Financial Reporting & Analytics',
                            'icon' => '💰',
                            'children' => [
                                ['Revenue', 'revenue.index'],
                                ['Expenses', 'expenses.index'],
                                ['Commissions', 'commissions.index'],
                                ['Financial Reports', 'reports.index'],
                                ['Analytics Dashboard', 'analytics-dashboard.index'],
                            ],
                        ],
                        [
                            'label' => 'Document & Visa Assistance',
                            'icon' => '📄',
                            'children' => [
                                ['Customer Documents', 'customer-documents.index'],
                                ['Visa Applications', 'visa-applications.index'],
                                ['Requirements', 'visa-requirements.index'],
                                ['Document Checklist', 'document-checklist.index'],
                                ['Expiration Tracking', 'document-expiration.index'],
                                ['Application Status', 'visa-status.index'],
                            ],
                        ],
                    ];
                @endphp
                @foreach ($nav as $section)
                    @if (empty($section['children']))
                        <a href="{{ route($section['route']) }}"
                           class="group mb-1.5 flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/70
                                  {{ request()->routeIs($section['route']) ? 'bg-white text-primary shadow-lg shadow-black/10 ring-1 ring-white/30' : 'text-white/75 hover:bg-white/10 hover:text-white' }}">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-sm transition {{ request()->routeIs($section['route']) ? 'bg-secondary text-white' : 'text-white/70 group-hover:bg-white/15' }}">{{ $section['icon'] }}</span>
                            <span class="flex-1 text-sm font-medium">{{ $section['label'] }}</span>
                            @if (request()->routeIs($section['route']))
                                <span class="h-1.5 w-1.5 rounded-full bg-secondary"></span>
                            @endif
                        </a>
                    @else
                        @php
                            $sectionActive = collect($section['children'])->contains(function ($item) {
                                [$childLabel, $childRoute] = $item;
                                return Route::has($childRoute)
                                    ? request()->routeIs(str($childRoute)->before('.') . '.*')
                                    : request()->routeIs('module.placeholder') && request()->route('module') === $childRoute;
                            });
                        @endphp
                        <details class="group mb-2" {{ $sectionActive ? 'open' : '' }}>
                            <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2.5 text-[11px] font-semibold uppercase tracking-[0.13em] text-white/55 transition hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/70 {{ $sectionActive ? 'bg-white/10 text-white' : '' }}">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-[12px]">{{ $section['icon'] }}</span>
                                    {{ $section['label'] }}
                                </span>
                                <span class="flex items-center gap-2">
                                    <span class="text-sm text-white/40 transition-transform group-open:rotate-180" aria-hidden="true">⌄</span>
                                </span>
                            </summary>
                            <div class="mt-1.5 space-y-1 border-l border-white/15 pl-2">
                                @foreach ($section['children'] as $childIndex => [$label, $route])
                                    @php
                                        $isNamedRoute = Route::has($route);
                                        $href = $isNamedRoute ? route($route) : route('module.placeholder', ['module' => $route]);
                                        $routePrefix = explode('.', $route)[0];
                                        $active = $isNamedRoute
                                            ? request()->routeIs($routePrefix . '.*')
                                            : request()->routeIs('module.placeholder') && request()->route('module') === $route;
                                    @endphp
                                    <a href="{{ $href }}"
                                       class="group flex min-w-0 items-center gap-2.5 rounded-lg px-3 py-2 text-[13px] transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/70
                                            {{ $active ? 'bg-secondary/90 font-medium text-white shadow-sm ring-1 ring-white/10' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                                        <span class="w-5 shrink-0 text-[10px] font-semibold tracking-wider {{ $active ? 'text-white/80' : 'text-white/30 group-hover:text-accent' }}">{{ str_pad($childIndex + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="min-w-0 truncate">{{ $label }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
            </nav>
            <div class="border-t border-white/10 bg-black/10 px-5 py-4 text-xs text-white/60">
                <div class="flex items-center justify-between gap-3">
                    <span>&copy; {{ date('Y') }} Holiday Travelers</span>
                </div>
                <div class="mt-3 flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/35">
                    <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                    All systems operational
                </div>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex min-w-0 flex-1 flex-col overflow-y-auto">
            <header class="sticky top-0 z-20 flex items-center justify-between border-b border-border bg-card/95 px-4 py-4 shadow-sm backdrop-blur-md dark:bg-gray-800/95 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" id="sidebar-toggle" aria-label="Open navigation" title="Open navigation"
                            class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-2xl border border-white/15 bg-primary text-white shadow-lg shadow-primary/25 transition hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent/70 active:scale-95">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true">
                            <line x1="6" y1="8" x2="18" y2="8" />
                            <line x1="6" y1="12" x2="18" y2="12" />
                            <line x1="6" y1="16" x2="18" y2="16" />
                        </svg>
                    </button>
                    <div class="min-w-0">
                    <p class="mb-1 hidden text-[11px] font-semibold uppercase tracking-[0.18em] text-gray-400 sm:block">Holiday Travelers / Back Office</p>
                    <h1 class="truncate font-heading text-lg font-semibold text-primary">@yield('title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden items-center gap-2 rounded-full border border-success/20 bg-success/10 px-3 py-1.5 text-xs font-medium text-success md:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-success"></span>
                        System online
                    </span>
                    <button type="button" id="theme-toggle" aria-label="Switch to dark mode" title="Switch to dark mode"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-border text-primary transition hover:bg-gray-50">
                        <span id="theme-icon" aria-hidden="true">☾</span>
                    </button>
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                        @csrf
                        <button type="submit" class="font-button rounded-lg bg-secondary px-4 py-2 text-sm text-white transition hover:opacity-90">
                            Log out
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 bg-[radial-gradient(circle_at_top_right,_rgba(111,169,230,0.10),_transparent_32rem)] p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 rounded-lg bg-success/10 text-success border border-success/30 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="mx-auto w-full max-w-[1600px]">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        function updateThemeControl() {
            const dark = document.documentElement.classList.contains('dark');
            themeIcon.textContent = dark ? '☀' : '☾';
            themeToggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
            themeToggle.setAttribute('title', dark ? 'Switch to light mode' : 'Switch to dark mode');
        }

        themeToggle.addEventListener('click', () => {
            const dark = document.documentElement.classList.toggle('dark');
            localStorage.theme = dark ? 'dark' : 'light';
            updateThemeControl();
        });

        updateThemeControl();

        const sidebar = document.getElementById('app-sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarToggle = document.getElementById('sidebar-toggle');

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        });
        sidebarOverlay?.addEventListener('click', closeSidebar);
        sidebar?.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));
    </script>
</body>
</html>
