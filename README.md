# Holiday Travelers Inc. — Back Office System

**DESIGN AND DEVELOPMENT OF A BACK OFFICE FOR TRAVEL AND TOUR AGENCY MANAGEMENT WITH AI-ASSISTED RESOURCE PLANNING FOR MARKETING, BUSINESS PARTNERS AND SUPPLIERS**

A web-based back-office application that runs the day-to-day operations of a travel
and tour agency — tour packages, bookings, payments, business partners, suppliers,
marketing, staff, finances, documents and reports — and layers an
**AI-Assisted Resource Planning** engine on top that turns that operational data into
actionable recommendations.

---

## Table of Contents

1. [What This System Is](#1-what-this-system-is)
2. [Tech Stack](#2-tech-stack)
3. [High-Level Architecture](#3-high-level-architecture)
4. [How a Request Flows Through the System](#4-how-a-request-flows-through-the-system)
5. [Authentication and Access Control](#5-authentication-and-access-control)
6. [The Modules — How Each One Works](#6-the-modules--how-each-one-works)
7. [Money: How Revenue Is Calculated](#7-money-how-revenue-is-calculated)
8. [AI-Assisted Resource Planning (Core Feature)](#8-ai-assisted-resource-planning-core-feature)
9. [Background / Scheduled Jobs](#9-background--scheduled-jobs)
10. [REST API](#10-rest-api)
11. [Data Model Overview](#11-data-model-overview)
12. [Project Structure](#12-project-structure)
13. [Getting Started Locally](#13-getting-started-locally)
14. [Environment Variables](#14-environment-variables)
15. [Deployment](#15-deployment)
16. [Roadmap](#16-roadmap)

---

## 1. What This System Is

The Back Office is the **internal control panel** for a travel agency. Staff log in to:

- Create and manage **tour packages** (domestic / international) with pricing, slots and duration.
- Capture and track **bookings** through their full lifecycle, with payments, invoices and receipts.
- Manage **business partners** (agencies, corporate accounts, affiliates) and their commissions.
- Manage **suppliers** (hotels, airlines, transport, tour guides), their contracts, rates and availability.
- Plan and track **marketing campaigns**, promotions, discount codes and leads.
- Manage **staff** — profiles, tasks, scheduling, performance and commissions — plus role permissions.
- Handle **documents and visa assistance** for customers.
- View **financials** — revenue, expenses, commissions — and generate **reports**.
- Consume **AI recommendations** for demand forecasting, supplier allocation and marketing budget.

Everything revolves around the **booking**. A booking links a customer to a tour
package, optionally to a business partner (who earns a commission), and accumulates
payments and invoices. Most other modules feed into or read from that central workflow.

---

## 2. Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 (PHP 8.2) |
| Frontend | Laravel Blade templates + Tailwind CSS |
| Build tool | Vite (`laravel-vite-plugin`) |
| Database | PostgreSQL |
| Web auth | Laravel session auth + Sanctum |
| Social auth | Laravel Socialite (Google) |
| Roles/Permissions | `spatie/laravel-permission` + a custom `EnsurePermission` middleware |
| API auth | Laravel Sanctum (token-based) |
| Outbound HTTP | Laravel `Http` facade (for the optional Anthropic LLM call) |
| CI/CD | GitHub Actions → Render (`render.yaml`) |

---

## 3. High-Level Architecture

```
Browser / API client
        │
        ▼
  Laravel HTTP Kernel
        │
        ├── Routes (routes/web.php → back-office UI, routes/api.php → REST API)
        │
        ├── Middleware
        │     ├── 'auth'       → must be logged in (session or Sanctum token)
        │     └── 'permission' → EnsurePermission maps route → required permission
        │
        ▼
  Controllers (app/Http/Controllers)
        │
        ├── use Models (app/Models) for Eloquent DB access
        ├── use Support\Revenue for all money figures
        └── use Services\AIResourcePlanningService for recommendations
        │
        ▼
  Views (resources/views/*.blade.php)  ── or ── JSON (API controllers)
```

Three cross-cutting pieces do heavy lifting and are worth knowing up front:

- **`app/Support/Revenue.php`** — the single source of truth for every money figure
  in the system (dashboard, reports, booking finance). Nothing computes revenue
  inline; everything calls this class.
- **`app/Http/Middleware/EnsurePermission.php`** — decides which permission each
  route requires and blocks users who lack it.
- **`app/Services/AIResourcePlanningService.php`** — the recommendation engine.

---

## 4. How a Request Flows Through the System

1. **Entry** — `public/index.php` boots Laravel. All requests route through
   `routes/web.php` (UI) or `routes/api.php` (REST).
2. **`/` redirects to login.** Guests are handled by the `guest` middleware group;
   everything else sits behind `['auth', 'permission']`.
3. **Auth check** — an unauthenticated request is bounced to `login`. Authenticated
   users continue.
4. **Permission check** — `EnsurePermission` derives the required permission from the
   route name (via a prefix map) and verifies the user's role has it. It also rejects
   **inactive accounts** (any status other than `active` → 403).
5. **Controller executes** — validates input, queries models, applies business rules
   (e.g. the booking controller runs its work inside a DB transaction).
6. **Response** — either a **Blade view** (HTML) for the UI, a **redirect with a flash
   message**, or **JSON** for API routes.

---

## 5. Authentication and Access Control

### Signing in

There are **two ways** to authenticate, both handled by
`App\Http\Controllers\Auth\LoginController`:

**A. Email + password + email verification code (two-step)**
1. User submits email + password. `Auth::validate()` checks the credentials.
2. If valid, a random **6-digit code** is generated, hashed, and stored in the
   session with a **10-minute expiry**. The plain code is emailed via `Mail::raw()`.
3. User is redirected to `login.verify` and enters the code.
4. The code is checked against the stored hash and expiry; on success the user is
   logged in, the session is regenerated, and they land on the dashboard.

**B. Google OAuth (Socialite)**
1. `redirectToGoogle` sends the user to Google.
2. `handleGoogleCallback` pulls the Google account, then enforces the **allowlist**:
   if `GOOGLE_ALLOWED_DOMAINS` or `GOOGLE_ALLOWED_EMAILS` are set, only matching
   accounts are admitted.
3. The user is `firstOrCreate`'d by email (new users get a random password and a
   verified email), logged in, and sent to the dashboard.

**Logout** invalidates the session and CSRF token and returns to login. It is
registered with `withoutMiddleware(ValidateCsrfToken::class)` so it never fails on an
expired CSRF token.

### Roles and permissions

Roles are stored on `users.role`: **admin, manager, agent, staff**.

- `User::hasPermission()` is the gate. **`admin` always passes.** Otherwise the
  permission list is read from the `role_permissions` table (managed in the UI);
  if that role has no stored configuration, sensible defaults apply (manager gets
  broad access, agent gets bookings + documents, staff gets dashboard + bookings).
- The eight permissions are:
  `view_dashboard`, `manage_staff`, `manage_tours`, `manage_bookings`,
  `manage_partners`, `manage_marketing`, `manage_documents`, `view_reports`.
- **Admins** manage the mapping on the **Roles & Permissions** page
  (`RolePermissionController`) — the admin role is always forced to *all* permissions.

`EnsurePermission` maps route-name prefixes to permissions, for example:

| Route prefix | Required permission |
|---|---|
| `dashboard` | `view_dashboard` |
| `packages.`, `tour-schedule.`, `ai-planning.` | `manage_tours` |
| `bookings.`, `payments.`, `payment-methods.` | `manage_bookings` |
| `partners.`, `suppliers.`, `supplier-*.` | `manage_partners` |
| `campaigns.`, `leads.`, `promotions.`, `discount-codes.` | `manage_marketing` |
| `staff-profiles.`, `tasks.`, `roles-permissions.` | `manage_staff` |
| `reports.`, `revenue.`, `expenses.`, `commissions.` | `view_reports` |
| `customer-documents.`, `visa-*.`, `document-*.` | `manage_documents` |

Routes whose workflows aren't finished yet resolve to a navigation placeholder
(`ModulePlaceholderController`) but **still enforce the matching permission**.

---

## 6. The Modules — How Each One Works

### Dashboard (`DashboardController`)
The landing page. Calculates KPIs straight from the database:
total bookings, revenue collected this month, booked value, outstanding balance,
active partners, active suppliers, running campaigns — plus a **monthly bookings
trend** for the current year, the **top destination** by booking count, the **best
marketing channel** by conversions, and the 5 most recent bookings. Money figures
come from `Support\Revenue`.

### Tour Packages & Bookings (`TourPackageController`, `BookingController`)
The operational heart of the system.

**Booking lifecycle:** `pending → confirmed → completed / cancelled / refunded`.

Creating a booking (`BookingController::store`) does the following **inside a
database transaction**:
1. Validates package, optional partner, customer details, passengers, travel date/time.
2. If a **discount code** was supplied, it is looked up (case-insensitive), locked for
   update, and checked for active status, validity dates and usage limit.
3. Computes the discount (percentage or fixed), clamps it to the subtotal, and writes
   `subtotal_amount`, `discount_amount` and `total_amount`.
4. Generates a unique reference (`HTI-…`) and **increments the discount code's usage count**.
5. **Syncs the partner commission** for the booking.

Bookings can be filtered by travel month/year, paid month/year and status. Each
booking has a **finance page** where staff record payments, generate invoices and
print receipts, and delete payments. Payments feed directly into the revenue figures.

### Business Partners (`BusinessPartnerController`)
Directory of agencies / corporate accounts / affiliates, with commission rates and
status. `PartnerCommission` records are created and kept in sync from bookings, and
are later approved / marked paid on the **Commissions** page (which drives the
"partners paid" figure in reports).

### Suppliers (`SupplierController`, `SupplierOperationsController`)
Supplier directory (hotels, airlines, transport, guides) with base rates, reliability
ratings and status (active / inactive / blacklisted). Operations screens add
**contracts, rates, availability and performance** records — these ratings and rates
are exactly what the AI supplier-allocation recommendation ranks against.

### Marketing (`MarketingCampaignController`, `LeadController`, `MarketingOperationsController`)
- **Campaigns** — channel (email, social, referral, ads, events), budget vs. actual
  spend, and conversions. Campaign data feeds the AI marketing-budget recommendation
  and the dashboard's "best channel".
- **Leads** — capture and conversion pipeline: new → contacted → qualified → converted.
- **Promotions & discount codes** — discount codes are validated and consumed by the
  booking flow.
- **Marketing calendar & campaign analytics** views.

### Staff (`StaffProfileController`, `AgentProfileController`, `StaffOperationController`)
Staff/agent profiles (department, job title, phone, status, role), plus **tasks,
scheduling and performance** records. Staff schedules/assignments are referenced by
tour planning and the AI readiness checks.

### Tour Planning (`TourPlanningController`)
Tour **schedules**, **availability**, **resource allocation**, **staff assignment**
and a **resource calendar**. These connect packages → dates → allocated resources →
assigned staff, and are used by the AI engine to detect upcoming tours that are
under-resourced or missing staff.

### Financial Operations (`FinancialOperationsController`)
Revenue view, **expenses** (create + list), **commissions** (approve / mark paid) and
an analytics dashboard. All revenue math delegates to `Support\Revenue`.

### Documents & Visa Assistance (`DocumentOperationsController`)
Customer **documents**, **visa applications**, **visa requirements**, a **document
checklist**, **document expiration** tracking and **visa status** tracking.

### Reports (`ReportController`)
Aggregated business reporting: total revenue, refunds, partner commissions paid,
campaign spend, net margin, monthly revenue series and channel conversions. Two
**CSV exports** are available (bookings export and financial export) via
`response()->streamDownload()`.

### Module placeholders
Navigation entries whose full workflow is scheduled for a later release route to
`ModulePlaceholderController`, which renders a "coming soon" page — but the
permission check still applies.

---

## 7. Money: How Revenue Is Calculated

**All money figures flow through `app/Support/Revenue.php`.** It uses a **cash basis**:
money is recognised in the month it was actually **received** (`payments.paid_at`),
not the month the trip departs. Refunds are **subtracted** from revenue.

Key definitions used everywhere:

- **Received payments** = `status IN ('paid', 'partial')`.
- **Committed bookings** = `status IN ('confirmed', 'completed')`.
- `collected()` = gross received − refunded.
- `collectedForMonth()` / `monthlyCollected()` = same, bucketed by payment month.
- `collectedForTravelMonth()` / `monthlyCollectedByTravelMonth()` = same money,
  bucketed by the **travel** month instead.
- `bookedValue()` = sum of `total_amount` on committed bookings (accrual context).
- `outstanding()` = for each committed booking, how much of `total_amount` is still
  unpaid.

Because there is a single class for this, the dashboard, reports and booking finance
pages can never disagree about what "revenue" means.

---

## 8. AI-Assisted Resource Planning (Core Feature)

Implemented in **`app/Services/AIResourcePlanningService.php`** and surfaced on the
**AI Resource Planning** page. It converts operational data into structured
recommendations, each saved to the `ai_resource_plans` table with a confidence score
and a status (`generated → reviewed → applied / dismissed`).

### What it produces

| Plan type | What it does |
|---|---|
| `demand_forecast` | Projects next-period **passengers** per package from the last 6 months of confirmed bookings, using a **weighted moving average adjusted by trend**. Outputs forecast pax, recommended slots, capacity gap and a confidence score. |
| `supplier_allocation` | Ranks **active suppliers** in a category (and optional location) by **reliability rating**, then **base rate**, returning the top candidates and a top pick. |
| `marketing_budget` | Computes **cost-per-conversion** per channel and recommends the channel with the lowest cost per conversion, with a suggested budget share. |
| `anomaly_alert` | Flags operational problems: a package facing a **capacity shortfall**, or an upcoming tour with **resource shortfall / missing staff**. 
| `partner_matching` | Reserved in the schema for score-based partner suggestions (extendable). |

### How a recommendation is generated

1. The service builds a **data snapshot** (`input_snapshot`) from live tables.
2. It applies the rule/model above to produce a structured `recommendation`.
3. It writes a **plain-language summary**.
4. **Optional LLM enrichment:** if `AI_API_KEY` is configured, the summary is
   rewritten by a call to the **Anthropic Messages API** (`enrichSummaryWithLLM`);
   if the key is absent or the call fails, it **falls back to the rule-based
   summary** — so the module always works, key or no key.
5. The plan is persisted with `status = 'generated'` and a confidence score.

### Manual vs. automatic

- **Manual:** staff trigger individual forecasts/recommendations from the AI Planning page.
- **Automatic:** the `ai:resource-plan` Artisan command runs the **full planning
  cycle** (`runAutomaticPlanning()`) — demand forecast for every draft/published
  package, capacity alerts, supplier recommendations for every active category,
  the marketing-budget recommendation, and readiness checks over tours departing in
  the **next 30 days**. It is scheduled **daily at 01:00** in `routes/console.php`.

---

## 9. Background / Scheduled Jobs

Registered in `routes/console.php` and `app/Console/Commands`:

| Command | Purpose | Schedule |
|---|---|---|
| `ai:resource-plan` | Run the full AI planning cycle | Daily at 01:00 |
| `SyncBookingPaymentStatus` | Keep booking payment status aligned with its payments | (command available; register on demand) |

---

## 10. REST API

Defined in `routes/api.php`, protected by `auth:sanctum`. Intended for a future
customer-facing website or mobile app to consume the same data.

| Method | Endpoint | Controller |
|---|---|---|
| GET | `/api/user` | current authenticated user |
| GET/POST/GET | `/api/bookings` | `Api\BookingApiController` (index, store, show) |
| GET | `/api/partners` | `Api\BusinessPartnerApiController` (index, show) |
| GET | `/api/suppliers` | `Api\SupplierApiController` (index, show) |
| GET | `/api/ai-plans` | `Api\AiResourcePlanApiController` (index) |

API clients authenticate with a **Sanctum token**; the web UI uses session auth.

---

## 11. Data Model Overview

Core tables (from `database/migrations`):

- **Identity:** `users` (with staff profile fields + `role`/`department`/`status`),
  `role_permissions`, `cache`, sessions.
- **Core operations:** `tour_packages`, `bookings`, `booking_status_histories`,
  `payments`, `invoices`, `payment-methods`.
- **Partners & suppliers:** `business_partners`, `partner_commissions`, `suppliers`,
  `supplier_contracts`, `supplier_rates`, `supplier_availability`, `supplier_performance`.
- **Marketing:** `marketing_campaigns`, `leads`, `promotions`, `discount_codes`.
- **Staff:** `staff_*` (tasks, schedules, commissions, performance).
- **Tour planning:** `tour_schedules`, `resource_allocations`, `staff_assignments`.
- **Finance:** `expenses`.
- **Documents/visa:** `documents`, `visa_applications`, `visa_requirements`.
- **AI:** `ai_resource_plans`.

Notable relationships: a **Booking** belongs to a **TourPackage** and optionally a
**BusinessPartner**, and has many **Payments**, **Invoices** and **StatusHistories**.
Payments are the basis for all revenue reporting.

---

## 12. Project Structure

```
app/
  Console/Commands/         ai:resource-plan, SyncBookingPaymentStatus
  Http/Controllers/         One controller per module (web UI)
  Http/Controllers/Api/     REST API controllers
  Http/Middleware/          EnsurePermission (role → permission mapping)
  Models/                   Eloquent models
  Services/                 AIResourcePlanningService (recommendation engine)
  Support/                  Revenue (single source of truth for money)
config/                     Laravel config (services.php holds Google + AI keys)
database/migrations/        Schema for every module
database/seeders, factories Demo data
resources/views/            Blade views (layouts, auth, dashboard, each module)
resources/css/app.css       Tailwind entrypoint (brand theme)
routes/web.php              Back-office UI routes
routes/api.php              REST API routes
routes/console.php          Scheduled commands
.github/workflows/ci.yml    GitHub Actions pipeline
render.yaml                 Render deployment blueprint
tailwind.config.js          Brand theme (colors + fonts)
```

---

## 13. Getting Started Locally

Prerequisites: PHP 8.2+, Composer, Node.js + npm, and PostgreSQL.

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# create a PostgreSQL database matching your .env (default: holiday_travelers), then:
php artisan migrate
php artisan db:seed        # optional demo data

npm run build              # or `npm run dev` while developing

php artisan serve          # http://localhost:8000
```

The root URL redirects to `/login`. Create or seed an **admin** user to sign in and
access every module.

> **Note on the repository layout:** there is currently a nested duplicate folder
> `holiday-travelers-backoffice/` inside the project that contains copies of many of
> the same files. The application uses the files at the project root (`app/`,
> `routes/`, `resources/`, …). The duplicate should be removed to avoid confusion.

---

## 14. Environment Variables

Key values in `.env` (see `.env.example`):

| Variable | Purpose |
|---|---|
| `APP_URL` | Base URL (used to build the Google redirect URI) |
| `DB_*` | PostgreSQL connection (`DB_CONNECTION=pgsql`) |
| `SESSION_DRIVER=database` | Sessions stored in the DB |
| `MAIL_*` | SMTP (Gmail) used to send the login verification code. `MAIL_CODE_RECIPIENT` overrides who receives it |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` / `GOOGLE_REDIRECT_URI` | Google OAuth login |
| `GOOGLE_ALLOWED_DOMAINS` / `GOOGLE_ALLOWED_EMAILS` | Optional comma-separated allowlists restricting who may sign in with Google |
| `SANCTUM_STATEFUL_DOMAINS` | Domains allowed to use stateful session auth for the API |
| `AI_PROVIDER` / `AI_API_KEY` | Optional AI summary enrichment (Anthropic). **Leave the key blank to use rule-based summaries only.** |

---

## 15. Deployment

- **CI** — `.github/workflows/ci.yml` installs dependencies, migrates against
  PostgreSQL, lints with Pint, and runs the test suite on every push/PR.
- **CD** — `render.yaml` is a one-click Render blueprint (web service + managed
  PostgreSQL).

---

## 16. Roadmap

**Phase 1 — Complete the core back office**
Finish remaining CRUD polish, add seeders/factories for realistic demo data, finalise
role permissions and user invitations, and expand booking status history, receipts,
cancellations and refunds. Add filters, sorting and pagination across screens.

**Phase 2 — Improve daily operations**
Email notifications (confirmations, reminders, unpaid bookings, expiring contracts),
full customer profiles, contract uploads with expiry reminders, a unified operations
calendar, and package inventory controls to prevent overbooking.

**Phase 3 — Reporting and business intelligence**
PDF/Excel exports, profit-margin and conversion-rate metrics, richer report filters,
and dashboard alerts for low availability, overdue payments and declining performance.

**Phase 4 — Expand AI assistance**
Lead-priority scoring, pricing recommendations from demand and supplier cost,
cancellation-risk prediction, campaign-content suggestions, and showing the reasons
and source metrics behind every recommendation.

**Phase 5 — Security and reliability**
Two-factor authentication, expanded audit logging, automated backups with soft
deletes, feature/workflow tests for every module, and API rate limiting, versioning
and documentation.
