# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PKLv2 is a Laravel 13 PKL (Praktek Kerja Lapangan) management system for UBSI. It manages student proposals, reports, grades, and integrates academic advisors (Dosen PA) and industry mentors.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3 (platform locked to `8.3.27` in `composer.json`)
- **Frontend**: TailwindCSS 4, Alpine.js 3 (with anchor/collapse/focus/intersect/morph/persist), Vite 6
- **Database**: SQLite (dev), MySQL/PostgreSQL supported; tests use SQLite `:memory:`
- **Auth**: Laravel Breeze + custom OTP verification + Google OAuth (Socialite)
- **PDF**: DomPDF (`barryvdh/laravel-dompdf`)
- **Excel**: Maatwebsite Excel
- **Audit**: Owen-It Laravel Auditing
- **Browser tests**: Laravel Dusk

## Key Commands

```bash
# One-shot setup (deps, .env, key, migrate, build assets)
composer run setup

# Dev: 4 concurrent processes — server | queue listener | pail logs | vite
composer run dev

# Tests (clears config cache first)
composer run test
php artisan test --filter=ProposalControllerTest   # single test class
php artisan dusk                                    # browser tests

# Code quality
./vendor/bin/pint              # Laravel Pint (config in pint.json)
./vendor/bin/phpstan analyse   # Larastan level 8 (excludes app/View)

# Frontend
npm run dev      # Vite dev server
npm run build    # Production build

# Production deployment
composer run build:production  # Install deps (no-dev), clear caches, build assets
```

## Architecture

### URL Encryption — Critical

**All route IDs are encrypted. Never pass raw IDs in URLs.**

- `encryptUrl($id)` / `decryptUrl($encrypted)` — defined in `app/Helpers/helpers.php` (autoloaded via `composer.json`)
- `decryptUrl()` aborts 403 on failure
- Route param convention: `{encrypted}`, e.g. `Route::get('/akun/{encrypted}/edit', ...)`
- Always wrap IDs with `encryptUrl()` when generating URLs/redirects

### Role-Based Access Control

Four roles in `App\Enums\UserRole`: `admin`, `dosen`, `mahasiswa`, `mentor`.

- **Middleware (variadic):** `Route::middleware('role:admin,dosen')` — alias registered in `bootstrap/app.php`
- **User scopes:** `User::mahasiswa()`, `User::dosen()`, `User::mentor()`, `User::admin()`
- **User helpers:** `$user->isAdmin()`, `$user->isDosen()`, `$user->isMahasiswa()`, `$user->isMentor()`
- **Role-aware redirect:** `$user->role->dashboardRoute()` returns the role's home route name

### Authorization (Policies)

Authorization via Laravel policies in `app/Policies`:

- **ProposalPolicy** — controls proposal access and actions:
  - `viewAny()` — admin/dosen/mentor can see lists
  - `view()` — owner (mahasiswa), their dosen PA, their mentor, or admin
  - `update()` — owner only, and only if `nilai <= 0` (not yet graded)
  - `grade()` — dosen PA, mentor, or admin
  - `uploadLaporan()` / `resetLaporan()` — owner only, if not yet graded
- **UserPolicy**, **ErrorLogPolicy** — similar ownership/role patterns
- Policies use `match` expressions and check ownership via `dosen_pa` (name match) and `email_mentor` (username match) fields

### Auth Flow

1. Login (standard credentials or Google OAuth — endpoint at `/auth/google`)
2. OTP verification required via `CheckOtpVerified` — codes expire in **5 minutes**
3. Redirect to role-specific dashboard
4. Forgot password is OTP-based (request → verify OTP → reset)

### Services Layer

Domain logic lives in `app/Services` — add it here before bloating controllers:

- **Core**: `DashboardService`, `ProposalService`, `NilaiService`, `MahasiswaService`, `DataMahasiswaService`, `UserService`
- **Import/Export**: `ImportService`, `PdfService`
- **External APIs**:
  - `FonnteService` — WhatsApp notifications via Fonnte API, rate-limited (10 msg/min default, configurable via `services.fonnte.rate_limit`)
  - `GroqService` — AI chatbot (llama-3.3-70b-versatile) with role-specific system prompts; endpoint at `/ai/chat`

### Models & Scopes

- **User** — auditable, OTP fields, `role` cast to `UserRole` enum
- **ProposalMahasiswa** — auditable, key scopes:
  - `belumDinilai()` / `sudahDinilai()` — by `nilai` value
  - `magang()` / `msib()` — by `jns_pkl`
  - `byDosen($namaDosen)` — joins via `user.nama_dosen_pa`
  - `byMentor($emailMentor)` — via `email_mentor`
- **ActivityLog**, **ErrorLog**, **OpeningHour** (singleton-like — controls academic timeline)
- PKL types in `App\Enums\PklType`: `Magang`, `MSIB`

### Middleware Stack (`bootstrap/app.php`)

- Globally appended: `DdosProtection`, `SecurityHeaders`
- Aliased: `role` → `CheckRole`
- Trust proxies: all (`*`) with full forwarded-header set
- Exception reporter writes to `ErrorLog` table (skips console, redacts password fields)

### Scheduled Jobs

- `pkl:check-timeline` runs daily at **08:00** (`routes/console.php`) — broadcasts PKL opening emails and sends H-3 reminders to unregistered students based on `OpeningHour` settings

### Caching

Dashboard stats 60s, lists 120s, recent logs 30s. Default cache driver.

### File Storage

Non-standard filesystem configuration (`config/filesystems.php`):

- **local** disk: `storage/app/private` (not default `storage/app`) — for private files served via controller
- **public** disk: root configurable via `FILESYSTEM_PUBLIC_ROOT` env var (defaults to `public_path()`)
- Uploaded files (proposals, reports) go through `FileController` with authorization checks

### Mail & Notifications

Mail classes in `app/Mail`:

- `ResetPasswordOtp` — OTP-based password reset
- `ReminderNotification` — H-3 reminders for unregistered students
- `SystemNotification` — admin broadcasts (PKL opening announcements)

## Frontend

- Entry: `resources/css/app.css`, `resources/js/app.js`
- Charts: Chart.js + Moment adapter; date picker: Flatpickr
- AI chatbot widget available on all authenticated pages (POST to `/ai/chat`)

## Testing & Seeding

- PHPUnit forces SQLite `:memory:`, `array` cache, `sync` queue, `BCRYPT_ROUNDS=4` (see `phpunit.xml`)
- `DatabaseSeeder` default credentials:
  - Admin: `admin` / `admin123`
  - Dosen: `1234567890` / `dosen123`
  - Mahasiswa: `12345678` / `mhs123`
  - Mentor: `sari@perusahaan.com` / `mentor123`
  - Plus one `OpeningHour` row with default academic timeline
- `DummyDataSeeder` creates 10 extra mahasiswa with proposals
