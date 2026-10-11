@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<style>
    html.dark .dashboard-shell-header{border-color:rgba(255,255,255,.1);background:rgba(17,24,39,.95)}
    html.dark .dashboard-shell-header-label{color:#94a3b8}
    html.dark .dashboard-shell-header-title,html.dark .dashboard-shell-theme-toggle{color:#fff}
    html.dark .dashboard-shell-theme-toggle{border-color:rgba(255,255,255,.15)}
    html.dark .dashboard-shell-theme-toggle:hover{background:rgba(255,255,255,.1)}
    html.dark .dashboard-shell-main{background:#111827}
    .travel-dashboard{--dash-bg:#111827;--dash-panel:#0f2038;--dash-panel-soft:#122641;--dash-line:#203957;--dash-text:#e7eef8;--dash-muted:#8fa4bf;--dash-blue:#2284ed;--dash-green:#2bc48a;--dash-orange:#f6a23b;--dash-purple:#9b72e8;background:var(--dash-bg);color:var(--dash-text);min-height:100%;padding:1.25rem}
    .travel-dashboard *{box-sizing:border-box}
    .dash-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;margin-bottom:1.25rem}
    .dash-heading h2{margin:.2rem 0 0;font-size:1.8rem;font-weight:700;letter-spacing:-.03em;color:#f5f8fc}
    .dash-heading p,.dash-muted{color:var(--dash-muted)}
    .dash-eyebrow{color:#73b7ff;font-size:.66rem;font-weight:800;letter-spacing:.16em;text-transform:uppercase}
    .dash-date{display:flex;align-items:center;gap:.7rem;border:1px solid var(--dash-line);border-radius:.85rem;background:var(--dash-panel);padding:.65rem .9rem;color:#d5e2f3;font-size:.75rem}
    .dash-grid{display:grid;gap:.85rem}
    .dash-metrics{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:1rem}
    .dash-card{min-width:0;border:1px solid var(--dash-line);border-radius:1rem;background:var(--dash-panel);box-shadow:0 10px 28px rgba(0,0,0,.12)}
    .dash-metric{position:relative;display:flex;align-items:center;gap:.8rem;padding:1rem}
    .dash-icon{display:flex;width:2.65rem;height:2.65rem;flex:0 0 2.65rem;align-items:center;justify-content:center;border-radius:.75rem;background:rgba(34,132,237,.17);color:#8dc5ff;font-size:1.15rem}
    .dash-icon.green{background:rgba(43,196,138,.16);color:#6de0b4}
    .dash-icon.orange{background:rgba(246,162,59,.16);color:#ffc477}
    .dash-icon.purple{background:rgba(155,114,232,.18);color:#c5aaff}
    .dash-metric-label{color:#a8bad1;font-size:.72rem}
    .dash-metric-value{margin-top:.2rem;color:#f7f9fc;font-size:1.35rem;font-weight:750;line-height:1.2}
    .dash-metric-note{margin-top:.2rem;color:#8299b5;font-size:.64rem}
    .dash-main-grid{grid-template-columns:minmax(0,1.15fr) minmax(220px,.85fr) minmax(280px,1.15fr);margin-bottom:1rem}
    .dash-panel-head{display:flex;align-items:center;justify-content:space-between;gap:.75rem;border-bottom:1px solid var(--dash-line);padding:.9rem 1rem}
    .dash-panel-title{margin:0;color:#eef4fc;font-size:.88rem;font-weight:700}
    .dash-panel-caption{margin:.2rem 0 0;color:var(--dash-muted);font-size:.67rem}
    .dash-link{color:#69b4ff;font-size:.67rem;font-weight:700;text-decoration:none;white-space:nowrap}
    .dash-link:hover{color:#a6d3ff}
    .dash-list{padding:.2rem 1rem}
    .dash-tour,.dash-person,.dash-update,.dash-package{display:flex;align-items:center;gap:.7rem;border-bottom:1px solid rgba(32,57,87,.7);padding:.72rem 0}
    .dash-tour:last-child,.dash-person:last-child,.dash-update:last-child,.dash-package:last-child{border-bottom:0}
    .dash-thumb{display:flex;width:3.35rem;height:2.5rem;flex:0 0 3.35rem;align-items:center;justify-content:center;border-radius:.55rem;background:linear-gradient(140deg,#2476a8,#143858 64%,#23634f);color:#d5f0ff;font-size:.72rem;font-weight:800}
    .dash-item-main{min-width:0;flex:1}
    .dash-item-title{overflow:hidden;color:#e6eef8;font-size:.72rem;font-weight:650;text-overflow:ellipsis;white-space:nowrap}
    .dash-item-sub{margin-top:.2rem;color:#8fa4bf;font-size:.62rem}
    .dash-item-meta{display:flex;flex-shrink:0;flex-direction:column;align-items:flex-end;gap:.3rem}
    .dash-pill{display:inline-flex;align-items:center;border-radius:999px;background:rgba(34,132,237,.16);padding:.22rem .48rem;color:#88c5ff;font-size:.59rem;font-weight:700;white-space:nowrap}
    .dash-pill.status-confirmed,.dash-pill.status-published,.dash-pill.status-completed{background:rgba(43,196,138,.15);color:#68ddb0}
    .dash-pill.status-pending,.dash-pill.status-planned,.dash-pill.status-draft{background:rgba(246,162,59,.15);color:#ffc477}
    .dash-pill.status-cancelled,.dash-pill.status-archived{background:rgba(239,91,105,.14);color:#ff929b}
    .dash-person-avatar{display:flex;width:2.1rem;height:2.1rem;flex:0 0 2.1rem;align-items:center;justify-content:center;border:1px solid #365778;border-radius:50%;background:#1a3b5d;color:#b6d9fb;font-size:.68rem;font-weight:800}
    .dash-assistant{margin:1rem;border:1px solid #264a70;border-radius:.8rem;background:linear-gradient(140deg,#142c4a,#10233e);padding:1rem}
    .dash-assistant-top{display:flex;align-items:center;gap:.7rem}
    .dash-assistant-icon{display:grid;width:2.4rem;height:2.4rem;place-items:center;border-radius:.7rem;background:#174d7d;color:#8bd1ff;font-size:1rem}
    .dash-assistant p{color:#a8bad1;font-size:.68rem;line-height:1.55}
    .dash-button{display:inline-flex;align-items:center;justify-content:center;border-radius:.55rem;background:#1676d2;padding:.55rem .8rem;color:#fff;font-size:.67rem;font-weight:700;text-decoration:none}
    .dash-button:hover{background:#278ce9}
    .dash-quick{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:.5rem;margin-top:.75rem}
    .dash-quick a{border:1px solid var(--dash-line);border-radius:.6rem;padding:.55rem .6rem;color:#b7cae0;font-size:.62rem;text-decoration:none}
    .dash-quick a:hover{border-color:#398bd0;color:white}
    .dash-bottom-grid{grid-template-columns:minmax(0,1.55fr) minmax(240px,.8fr) minmax(240px,.9fr)}
    .dash-table-wrap{overflow-x:auto}
    .dash-table{width:100%;border-collapse:collapse;text-align:left;font-size:.66rem}
    .dash-table th{background:rgba(5,15,29,.3);color:#8fa4bf;font-size:.58rem;font-weight:750;letter-spacing:.08em;text-transform:uppercase}
    .dash-table th,.dash-table td{padding:.68rem .8rem;white-space:nowrap}
    .dash-table tbody tr{border-top:1px solid rgba(32,57,87,.7)}
    .dash-table tbody tr:hover{background:rgba(255,255,255,.025)}
    .dash-empty{padding:1.25rem 1rem;color:var(--dash-muted);font-size:.7rem;text-align:center}
    .dash-quick-links{display:flex;flex-wrap:wrap;align-items:center;gap:.5rem;margin-top:1rem}
    .dash-quick-links strong{margin-right:.25rem;color:#91a8c3;font-size:.65rem}
    .dash-quick-links a{border:1px solid var(--dash-line);border-radius:.55rem;background:var(--dash-panel);padding:.55rem .7rem;color:#c2d1e4;font-size:.65rem;text-decoration:none}
    .dash-quick-links a:hover{border-color:#398bd0;color:white}
    html:not(.dark) .travel-dashboard{--dash-bg:#f1f5f9;--dash-panel:#fff;--dash-panel-soft:#f8fafc;--dash-line:#dce5ef;--dash-text:#17324d;--dash-muted:#64748b;background:var(--dash-bg);color:var(--dash-text)}
    html:not(.dark) .dash-heading h2,html:not(.dark) .dash-metric-value,html:not(.dark) .dash-panel-title,html:not(.dark) .dash-item-title{color:#17324d}
    html:not(.dark) .dash-eyebrow{color:#2878c8}
    html:not(.dark) .dash-date{background:#fff;color:#334155}
    html:not(.dark) .dash-card{box-shadow:0 10px 28px rgba(23,50,77,.07)}
    html:not(.dark) .dash-metric-label{color:#52677e}
    html:not(.dark) .dash-metric-note,html:not(.dark) .dash-item-sub,html:not(.dark) .dash-panel-caption{color:#64748b}
    html:not(.dark) .dash-link{color:#176fba}
    html:not(.dark) .dash-link:hover{color:#0c4c85}
    html:not(.dark) .dash-tour,html:not(.dark) .dash-person,html:not(.dark) .dash-update,html:not(.dark) .dash-package,html:not(.dark) .dash-table tbody tr{border-color:#e5edf5}
    html:not(.dark) .dash-thumb{background:linear-gradient(140deg,#b8dcf5,#d9eaf5 64%,#c4e3d5);color:#17466a}
    html:not(.dark) .dash-pill{background:#e7f2ff;color:#1769aa}
    html:not(.dark) .dash-pill.status-confirmed,html:not(.dark) .dash-pill.status-published,html:not(.dark) .dash-pill.status-completed{background:#e4f7ef;color:#18734e}
    html:not(.dark) .dash-pill.status-pending,html:not(.dark) .dash-pill.status-planned,html:not(.dark) .dash-pill.status-draft{background:#fff3df;color:#9a5b08}
    html:not(.dark) .dash-pill.status-cancelled,html:not(.dark) .dash-pill.status-archived{background:#ffeaec;color:#aa3540}
    html:not(.dark) .dash-person-avatar{border-color:#c9d9e8;background:#eaf2f9;color:#285578}
    html:not(.dark) .dash-assistant{border-color:#cbdff0;background:linear-gradient(140deg,#f1f8ff,#e8f2fb)}
    html:not(.dark) .dash-assistant-icon{background:#dceeff;color:#1769aa}
    html:not(.dark) .dash-assistant p{color:#52677e}
    html:not(.dark) .dash-table th{background:#f1f5f9;color:#64748b}
    html:not(.dark) .dash-table tbody tr:hover{background:#f8fafc}
    html:not(.dark) .dash-quick a,html:not(.dark) .dash-quick-links a{border-color:#dce5ef;color:#38536e}
    html:not(.dark) .dash-quick a:hover,html:not(.dark) .dash-quick-links a:hover{border-color:#75acd8;color:#17324d}
    html:not(.dark) .dash-quick-links strong{color:#64748b}
    @media(max-width:1100px){.dash-main-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-assistant-panel{grid-column:1/-1}.dash-bottom-grid{grid-template-columns:minmax(0,1.4fr) minmax(220px,.8fr)}.dash-updates-panel{grid-column:1/-1}}
    @media(max-width:700px){.travel-dashboard{padding:1rem}.dash-heading{align-items:flex-start;flex-direction:column}.dash-heading h2{font-size:1.45rem}.dash-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.dash-main-grid,.dash-bottom-grid{grid-template-columns:minmax(0,1fr)}.dash-assistant-panel,.dash-updates-panel{grid-column:auto}.dash-metric{padding:.8rem}.dash-icon{width:2.35rem;height:2.35rem;flex-basis:2.35rem}.dash-metric-value{font-size:1.1rem}}
</style>

<div class="travel-dashboard">
    <div class="dash-heading">
        <div>
            <p class="dash-eyebrow">Travel operations</p>
            <h2>Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->role === 'admin' ? 'Admin' : explode(' ', auth()->user()->name)[0] }}!</h2>
            <p class="mt-1 text-sm dash-muted">Here is what is happening across your travel business.</p>
        </div>
        <div class="dash-date"><span aria-hidden="true">◷</span><span>{{ now()->format('D, M d, Y') }}<br><strong>{{ now()->format('g:i A') }}</strong></span></div>
    </div>

    <div class="dash-grid dash-metrics">
        @foreach ([
            ['Tour Packages', number_format($kpis['tour_packages']), 'Available in your package catalog', '▣', ''],
            ['Assigned Staff', number_format($kpis['assigned_staff']), 'Staff assigned to upcoming tours', '♧', 'green'],
            ['Packages Sold', number_format($kpis['packages_sold']), 'Packages with confirmed sales', '◇', 'purple'],
            ['Revenue This Month', '₱' . number_format($kpis['revenue_this_month'], 2), 'Cash received, net of refunds', '↗', 'orange'],
        ] as [$label, $value, $note, $icon, $color])
            <article class="dash-card dash-metric">
                <span class="dash-icon {{ $color }}" aria-hidden="true">{{ $icon }}</span>
                <div class="min-w-0">
                    <div class="dash-metric-label">{{ $label }}</div>
                    <div class="dash-metric-value">{{ $value }}</div>
                    <div class="dash-metric-note">{{ $note }}</div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="dash-grid dash-main-grid">
        <section class="dash-card">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">Upcoming Tours</h3><p class="dash-panel-caption">Scheduled departures from your tour planner</p></div>
                <a class="dash-link" href="{{ route('tour-schedule.index') }}">View schedules &rarr;</a>
            </div>
            <div class="dash-list">
                @forelse ($upcomingTours as $tour)
                    <div class="dash-tour">
                        <span class="dash-thumb" aria-hidden="true">{{ strtoupper(substr($tour->tourPackage?->destination ?? 'TOUR', 0, 2)) }}</span>
                        <div class="dash-item-main">
                            <div class="dash-item-title">{{ $tour->tourPackage?->name ?? 'Tour package' }}</div>
                            <div class="dash-item-sub">{{ $tour->destination ?: ($tour->tourPackage?->destination ?? 'Destination not set') }} · {{ $tour->schedule_date->format('M d, Y') }}</div>
                        </div>
                        <div class="dash-item-meta"><span class="dash-pill status-{{ $tour->status }}">{{ ucfirst($tour->status) }}</span><span class="dash-item-sub">{{ $tour->starts_at ? substr($tour->starts_at, 0, 5) : 'Time TBC' }}</span></div>
                    </div>
                @empty
                    <div class="dash-empty">No upcoming tours are scheduled. Add a departure in Tour Planning.</div>
                @endforelse
            </div>
        </section>

        <section class="dash-card">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">Assigned Staff</h3><p class="dash-panel-caption">Team members on upcoming departures</p></div>
                <a class="dash-link" href="{{ route('staff-assignment.index') }}">Manage &rarr;</a>
            </div>
            <div class="dash-list">
                @forelse ($assignedStaff as $assignment)
                    <div class="dash-person">
                        <span class="dash-person-avatar" aria-hidden="true">{{ strtoupper(substr($assignment->user?->name ?? '?', 0, 1)) }}</span>
                        <div class="dash-item-main">
                            <div class="dash-item-title">{{ $assignment->user?->name ?? 'Staff member' }}</div>
                            <div class="dash-item-sub">{{ ucfirst(str_replace('_', ' ', $assignment->assignment_role)) }} · {{ $assignment->tourSchedule?->tourPackage?->name ?? 'Tour' }}</div>
                        </div>
                        <span class="dash-pill status-{{ $assignment->status }}">{{ ucfirst($assignment->status) }}</span>
                    </div>
                @empty
                    <div class="dash-empty">No staff assignments are recorded for upcoming tours.</div>
                @endforelse
            </div>
        </section>

        <section class="dash-card dash-assistant-panel">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">AI Travel Assistant</h3><p class="dash-panel-caption">Plan demand and check trip readiness</p></div>
                <span class="dash-pill">Planning tools</span>
            </div>
            <div class="dash-assistant">
                <div class="dash-assistant-top">
                    <span class="dash-assistant-icon" aria-hidden="true">✦</span>
                    <div><div class="dash-item-title">Plan with your real tour data</div><div class="dash-item-sub">Open the existing resource planning tools.</div></div>
                </div>
                <p>Review upcoming schedules, forecast package demand, and check travel readiness from the planning workspace.</p>
                <a class="dash-button" href="{{ route('ai-planning.index') }}">Open AI Planning</a>
            </div>
        </section>
    </div>

    <div class="dash-grid dash-bottom-grid">
        <section class="dash-card">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">Recent Bookings</h3><p class="dash-panel-caption">Latest customer reservations in your system</p></div>
                <a class="dash-link" href="{{ route('bookings.index') }}">View all &rarr;</a>
            </div>
            <div class="dash-table-wrap">
                <table class="dash-table">
                    <thead><tr><th>Reference</th><th>Guest</th><th>Tour Package</th><th>Travel Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->reference_no ?? 'HT-' . $booking->id }}</td>
                                <td>{{ $booking->customer_name }}</td>
                                <td>{{ $booking->tourPackage?->name ?? 'Package not assigned' }}</td>
                                <td>{{ $booking->travel_date?->format('M d, Y') ?? '—' }}</td>
                                <td><span class="dash-pill status-{{ $booking->status ?? 'pending' }}">{{ ucfirst($booking->status ?? 'pending') }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="dash-empty">No bookings have been recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dash-card">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">Tour Packages</h3><p class="dash-panel-caption">Catalog packages and sales status</p></div>
                <a class="dash-link" href="{{ route('packages.index') }}">View all &rarr;</a>
            </div>
            <div class="dash-list">
                @forelse ($packages as $package)
                    <div class="dash-package">
                        <span class="dash-thumb" aria-hidden="true">{{ strtoupper(substr($package->destination, 0, 2)) }}</span>
                        <div class="dash-item-main">
                            <div class="dash-item-title">{{ $package->name }}</div>
                            <div class="dash-item-sub">{{ $package->duration_days }} days · ₱{{ number_format((float) $package->price, 2) }}</div>
                        </div>
                        <span class="dash-pill status-{{ $package->status }}">{{ ucfirst($package->status) }}</span>
                    </div>
                @empty
                    <div class="dash-empty">No tour packages found. Add a package to get started.</div>
                @endforelse
            </div>
        </section>

        <section class="dash-card dash-updates-panel">
            <div class="dash-panel-head">
                <div><h3 class="dash-panel-title">Travel Updates</h3><p class="dash-panel-caption">Current schedule and departure status</p></div>
                <a class="dash-link" href="{{ route('ai-planning.index') }}">Readiness &rarr;</a>
            </div>
            <div class="dash-list">
                @forelse ($upcomingTours->take(4) as $tour)
                    <div class="dash-update">
                        <span class="dash-icon {{ $tour->status === 'confirmed' ? 'green' : 'orange' }}" style="width:2rem;height:2rem;flex-basis:2rem;font-size:.85rem" aria-hidden="true">⌖</span>
                        <div class="dash-item-main">
                            <div class="dash-item-title">{{ $tour->destination ?: ($tour->tourPackage?->destination ?? 'Destination not set') }}</div>
                            <div class="dash-item-sub">{{ $tour->schedule_date->format('M d') }} · {{ $tour->tourPackage?->name ?? 'Tour' }}</div>
                        </div>
                        <span class="dash-pill status-{{ $tour->status }}">{{ ucfirst($tour->status) }}</span>
                    </div>
                @empty
                    <div class="dash-empty">Travel updates will appear when upcoming tours are scheduled.</div>
                @endforelse
            </div>
        </section>
    </div>

    <nav class="dash-quick-links" aria-label="Dashboard quick links">
        <strong>Quick links</strong>
        <a href="{{ route('reports.index') }}">▤ Reports</a>
        <a href="{{ route('staff-assignment.index') }}">♙ Staff assignments</a>
        <a href="{{ route('ai-planning.index') }}">✦ AI planning</a>
    </nav>
</div>
@endsection
