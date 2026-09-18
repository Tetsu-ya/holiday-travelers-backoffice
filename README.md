# Holiday Travelers Inc. — Back Office System

**DESIGN AND DEVELOPMENT OF BACK OFFICE FOR TRAVEL AND TOUR AGENCY MANAGEMENT
WITH AI-ASSISTED RESOURCE PLANNING FOR MARKETING, BUSINESS PARTNER AND SUPPLIER**

## Tech Stack
| Layer | Technology |
|---|---|
| Frontend | Laravel Blade + Tailwind CSS |
| Backend | Laravel 11 (PHP 8.2) |
| Database | PostgreSQL |
| Auth | Laravel Sanctum |
| API | REST (Sanctum-protected) |
| CI/CD | GitHub Actions → Render |

## Brand
- Theme: **Sky and Sunset**
- Colors: Deep Navy `#163B6D`, Sunset Orange `#F59B45`, Sky Blue `#6FA9E6`, Snow White `#F8FAFC`
- Fonts: Poppins (headings/buttons), Inter (body)
- Client logo included at `public/images/logo.png`

---

## Feature List

### 1. User & Access Management
- Role-based access (Admin, Marketing Staff, Partnership Officer, Supplier Coordinator)
- Sanctum-based login/logout, session + token auth for API clients
- Activity/audit logging
- Profile management

### 2. Dashboard & Analytics
- KPI overview: bookings, monthly revenue, active partners/suppliers, running campaigns
- Booking trend & revenue charts
- AI insights preview panel

### 3. Tour Package & Booking Management
- CRUD for domestic/international packages, pricing, slots, duration
- Booking lifecycle: pending → confirmed → completed/cancelled
- Payment status tracking (unpaid/partial/paid/refunded)

### 4. Business Partner Management
- Partner directory: agencies, corporate accounts, affiliates
- Commission rate & contract tracking
- Partner performance scoring (feeds into AI matching)

### 5. Supplier Management
- Supplier directory: hotels, airlines, transport, tour guides
- Base rates & reliability ratings
- Status control (active / inactive / blacklisted)

### 6. Marketing Management
- Campaign planning across channels (email, social, referral, ads, events)
- Budget vs. actual spend tracking
- Lead capture and conversion pipeline (new → contacted → qualified → converted)

### 7. AI-Assisted Resource Planning *(core module)*
Implemented in `app/Services/AIResourcePlanningService.php`:
- **Demand forecasting** — projects next-period booking volume per package from historical data
- **Supplier allocation recommendation** — ranks suppliers by reliability & cost for a category/location
- **Marketing budget allocation** — recommends the channel with the best cost-per-conversion
- **Partner-matching** *(extendable)* — score-based partner suggestion per region/package
- **Plain-language summaries** — optionally enriched via an LLM call (Anthropic API, configurable in `.env` as `AI_PROVIDER` / `AI_API_KEY`); falls back to a rule-based summary if no key is set
- All recommendations are logged to `ai_resource_plans` with a confidence score and status (generated/reviewed/applied/dismissed), shown on the **AI Resource Planning** dashboard page

### 8. Reports
- Sales & revenue, partner/supplier performance, marketing ROI
- Export-ready structure (PDF/Excel export can be added via `barryvdh/laravel-dompdf` / `maatwebsite/excel`)

### 9. REST API (`routes/api.php`)
- Sanctum-protected endpoints for bookings, partners, suppliers, AI plans
- Ready for a customer-facing site or mobile app to consume

### 10. DevOps
- `.github/workflows/ci.yml` — installs deps, runs migrations against Postgres, lints (Pint), runs tests on every push/PR
- `render.yaml` — one-click Render blueprint (web service + managed Postgres)

---

## Project Structureipconfig
```
app/
  Http/Controllers/        Web controllers per module
  Http/Controllers/Api/    REST API controllers
  Models/                  Eloquent models
  Services/AIResourcePlanningService.php   AI recommendation engine
database/migrations/       Schema for all modules
resources/views/           Blade views (layouts, auth, dashboard, ai-planning, ...)
resources/css/app.css      Tailwind entrypoint
routes/web.php             Back-office UI routes
routes/api.php             REST API routes
.github/workflows/ci.yml   GitHub Actions pipeline
render.yaml                Render deployment blueprint
tailwind.config.js         Brand theme (colors + fonts)
```

## Getting Started Locally
```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

# create a PostgreSQL database matching .env, then:
php artisan migrate

php artisan serve
```

## Still To Do (scaffold provided, wire up next)
- `App\Http\Controllers\Auth\LoginController`, `TourPackageController`, `BookingController`,
  `MarketingCampaignController`, `LeadController`, `ReportController`, and matching Api controllers
  (routes already reference them — same pattern as `BusinessPartnerController`/`SupplierController`)
- `TourPackage::published()` scope (or swap for `->where('status','published')`)
- Seeders/factories for demo data
- Spatie `laravel-permission` role/permission seeding
- Blade views for packages, bookings, marketing, reports (same layout/pattern as `partners`/`suppliers`)

## Recommended Product Roadmap

### Phase 1 — Complete the Core Back Office
- Finish CRUD controllers and Blade views for packages, bookings, campaigns, leads, and reports
- Add seeders and factories for realistic demo data
- Implement role-based permissions and user invitations
- Add booking status history, payment receipts, cancellations, and refunds
- Add filters, sorting, pagination, and validation across management screens

### Phase 2 — Improve Daily Operations
- Add email notifications for confirmations, reminders, unpaid bookings, and expiring contracts
- Add customer profiles with booking history, preferences, notes, and special requests
- Add supplier and partner contract uploads with expiration reminders
- Add a calendar for travel dates, package availability, supplier assignments, and campaign deadlines
- Add package inventory controls to prevent overbooking

### Phase 3 — Reporting and Business Intelligence
- Add PDF and Excel exports for sales, revenue, partner, supplier, and marketing reports
- Add profit margin, commission, conversion-rate, and campaign ROI metrics
- Add date, package, partner, supplier, and payment-status filters to reports
- Add dashboard alerts for low availability, overdue payments, and declining performance

### Phase 4 — Expand AI Assistance
- Add lead-priority scoring and recommended follow-up actions
- Add package pricing recommendations based on demand and supplier costs
- Add cancellation-risk prediction for upcoming bookings
- Add campaign-content suggestions and audience recommendations
- Show the reasons and source metrics behind every AI recommendation

### Phase 5 — Security and Reliability
- Add two-factor authentication and session management
- Expand audit logging for sensitive changes and API access
- Add automated database backups and record recovery with soft deletes
- Add feature and workflow tests for every major module
- Add API rate limiting, versioning, and API documentation

### Suggested First Milestone
Complete the core controllers and views, add demo data, implement permissions, and cover the booking workflow with tests. This creates a usable operational baseline before investing further in advanced AI features.
