# Haleys — Playhouse POS + Time Tracking SaaS

A multi-tenant POS system for indoor playhouses & kids play centers, built in **Laravel 11 + MySQL** with Tailwind CSS (via CDN). Each business has its own data, packages, children, sessions, receipts and reports.

> Brand name + logo are configurable on the fly via `.env` (`POS_BRAND_NAME`, `POS_BRAND_LOGO`) or `config/pos.php` — and through the in-app **Settings** page (admin only).

---

## ✨ Features

- **Multi-tenant**: every record is scoped by `business_id` via a global Eloquent scope.
- **Child CRUD**: name, age, gender, guardian, contacts, notes, photo, unique `child_code`.
- **Sessions**: time-based or unlimited packages, auto computed `expected_end_time`, remaining time, auto-expiry sweep, extensions, early end.
- **Pricing locked at start**: the package price is frozen on the session at the moment of check-in.
- **Receipts**: format `BUSINESSID-YYYYMMDD-XXXX`, printable view, JSON snapshot of child / package / session at the time of issuance.
- **Dashboard**: tablet-first, active children cards, remaining time, green/yellow/red status chips, quick extend & end actions.
- **Parent portal (no login)**: lookup by child code or QR code (`/lookup?code=...`).
- **Reports**: daily / monthly / historical with date range + status filters.
- **CSV export**: optimised streamed export (`reports.export`) + 1-click monthly export (`reports.exportMonth`).
- **Roles**: `admin`, `staff`. Parents do not register.
- **Audit logs** for session start / extend / early-terminate / end / expire.

---

## 🚀 Quick start (local)

Requirements: **PHP 8.2+**, **Composer**, **MySQL 8.x** (or MariaDB 10.6+), `pdo_mysql` extension.

```bash
# 1. Install PHP dependencies
composer install

# 2. Copy env & generate app key
cp .env.example .env
php artisan key:generate

# 3. Create a MySQL database called `haleys_pos`
#    (or edit DB_* values in .env)

# 4. Run migrations & seed demo data
php artisan migrate --seed

# 5. Link the public storage disk (for child photos & logo uploads)
php artisan storage:link

# 6. Serve
php artisan serve
```

Open **http://localhost:8000** — you'll see the parent lookup page. Staff login is at **/login**.

### Demo credentials (seeded)

| Role  | Email              | Password |
|-------|--------------------|----------|
| Admin | admin@haleys.test  | password |
| Staff | staff@haleys.test  | password |
 
The seeder also creates one business (`Haleys Playhouse`, code `HAL`), 4 packages and 4 sample children.

---

## 🧱 Project structure (only the meaningful bits)

```
app/
  Http/Controllers/      Auth, Dashboard, Child, PlaySession, Package, Receipt,
                         Report, ParentPortal, Settings, Staff
  Http/Middleware/       EnsureUserHasRole, EnsureTenantContext
  Models/                Business, User, Child, Package, PlaySession, Receipt,
                         AuditLog, BelongsToBusiness (trait)
  Services/              SessionService, ReceiptService, ReportService
config/
  pos.php                Brand name / logo / currency / status thresholds
database/
  migrations/            8 migrations (businesses, users+sessions, cache,
                         packages, children, play_sessions, receipts, audit_logs)
  seeders/DatabaseSeeder.php
resources/views/         Blade views (Tailwind via CDN; Inter font)
routes/web.php           All routes (no API, Blade-driven)
```

---

## 🔐 Tenant isolation

Models that should be tenant-scoped use the `BelongsToBusiness` trait. It:

1. Adds a global scope that constrains queries to `business_id = current tenant`.
2. Auto-fills `business_id` on `creating` if missing.
3. Resolves the tenant from either:
   - `app('tenant.business_id')` (set by the `tenant` middleware), or
   - the authenticated user's `business_id`.

`EnsureTenantContext` middleware is applied to every authenticated route. The parent portal sets the tenant from the looked-up `child_code` only for that request.

---

## 📑 Receipt numbering

`{BUSINESS.code}-{YYYYMMDD}-{XXXX}` — sequence is per business per day, with a `lockForUpdate()` to keep it collision-safe.

Example: `HAL-20260201-0001`.

---

## ⏱ Background expiry sweep

Call `php artisan sessions:sweep` (or schedule it every minute):

```cron
* * * * * cd /path/to/haleys && php artisan sessions:sweep >> /dev/null 2>&1
```

This marks active sessions whose `expected_end_time` has passed as `expired` and generates their receipts.

> The dashboard also auto-sweeps on every load, so a cron is optional for low-volume sites.

---

## 🎨 Rebranding

Three ways, from quickest to most permanent:

1. **In-app (admin → Settings)** — change name, code prefix, contact, currency, upload logo.
2. **`.env`** — set `POS_BRAND_NAME`, `POS_BRAND_LOGO`, `POS_CURRENCY_SYMBOL`. Restart `php artisan serve` after.
3. **`config/pos.php`** — change defaults.

Logo file should live in `/public/assets/` (e.g. `/assets/logo.svg`) or be uploaded via the Settings page (it'll be saved into `storage/app/public/branding/`).

---

## 🧪 Notes & caveats

- Tailwind is loaded from the CDN for zero-build local development. If you'd like a production build, install Vite/Tailwind manually and replace the CDN tag in `resources/views/layouts/base.blade.php`.
- Photo uploads use the `public` disk — make sure `php artisan storage:link` has been run once.
- Multi-business support is wired but the UI exposes a single business per logged-in user. To onboard more businesses, insert another `businesses` row and create users with the matching `business_id`.

-- e.g. via MySQL or `php artisan tinker`
INSERT INTO businesses (name, slug, code, currency_symbol, created_at, updated_at)
VALUES ('BouncyTown', 'bouncytown', 'BNC', '$', NOW(), NOW());

INSERT INTO users (business_id, name, email, password, role, is_active, created_at, updated_at)
VALUES (2, 'BouncyTown Owner', 'owner@bouncytown.test',
        '<bcrypt hash of password>', 'admin', 1, NOW(), NOW());

Have fun running the playhouse 🛝
