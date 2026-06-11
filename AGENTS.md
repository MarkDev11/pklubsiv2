# AGENTS.md

PKLv2 is a Laravel 13 PKL (Praktek Kerja Lapangan) management system for a higher education institution (UBSI). It manages student proposals, reports, grades, and integrates with academic advisors (Dosen PA) and industry mentors.

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3 (platform locked to `8.3.27` in `composer.json`)
- **Frontend**: TailwindCSS 4, Alpine.js 3, Vite
- **Database**: SQLite (dev/testing), supports MySQL/PostgreSQL
- **Auth**: Laravel Breeze + Google OAuth (Socialite). OTP only used for password reset, not login.
- **PDF**: DomPDF (`barryvdh/laravel-dompdf`)
- **Excel**: Maatwebsite Excel
- **Audit**: Owen-It Laravel Auditing
- **Browser Testing**: Laravel Dusk

## Developer Commands

Use these exact commands; do not guess alternatives:

```bash
# One-shot project setup (installs deps, creates .env, generates key, migrates, builds assets)
composer run setup

# Full dev environment – runs 4 concurrent processes via `concurrently`:
# Laravel server | Queue listener | Pail logs | Vite dev server
composer run dev

# Run tests (clears config cache first)
composer run test
# Or run a specific test class:
php artisan test --filter=ProposalControllerTest

# Code quality
./vendor/bin/pint              # Laravel Pint (preset: laravel, custom rules in pint.json)
./vendor/bin/phpstan analyse   # Larastan level 8 (excludes app/View)

# Browser tests
php artisan dusk
```

## Architecture Notes

### URL Encryption — Critical

**All route IDs are encrypted using custom helpers. Never pass raw IDs in URLs.**

- **Encrypt:** `encryptUrl($id)`
- **Decrypt:** `decryptUrl($encrypted)` (aborts 403 on failure)
- Route definitions use `{encrypted}` as the parameter name:  
  `Route::get('/akun/{encrypted}/edit', ...)`
- If you generate URLs or redirects, wrap IDs with `encryptUrl()`.

### Role-Based Access Control

Four roles in `App\Enums\UserRole`: `admin`, `dosen`, `mahasiswa`, `mentor`.

- **Middleware:** `CheckRole` — usage is variadic string args:  
  `Route::middleware('role:admin,dosen')`
- **User scopes:** `User::mahasiswa()`, `User::dosen()`, `User::mentor()`, `User::admin()`
- **User helpers:** `$user->isAdmin()`, `$user->isDosen()`, `$user->isMahasiswa()`, `$user->isMentor()`
- **Dashboard routes:** `$user->role->dashboardRoute()` returns the role-specific home route name.

### Auth Flow

1. Login (standard or Google OAuth)
2. Redirect to role-specific dashboard (no OTP verification step)
3. Forgot password uses OTP flow (request → verify OTP → reset)

**Note:** OTP verification after login is **NOT active**. The `CheckOtpVerified` middleware exists but is not registered in `bootstrap/app.php`. Login flows directly to the dashboard without OTP verification. OTP is only used for the forgot password flow.

### User Creation

**No self-registration.** Users cannot register themselves. User accounts can only be created by admins via:

- **Manual creation:** `/admin/akun/create` — Admin inputs user data, selects role, system auto-generates password
- **Excel import:** `/admin/import` — Bulk user creation via Excel file upload (uses `ImportUsersJob` queue)

There is no `/register` route or public signup form. All users (mahasiswa, dosen, mentor, admin) must be created by an administrator.

**Note:** Google OAuth is NOT a registration method. Users must exist in the database (with matching `email_bsi`) before they can login via Google. OAuth login attempts with unregistered `@bsi.ac.id` emails will be rejected with error "Email tidak terdaftar dalam sistem."

### PKL Jenis Workflow

**Flexible workflow: Both admin and mahasiswa can set jenis PKL type.**

- **Admin can optionally set jenis:** When admin creates a mahasiswa account (manual or import), the `users.jenis` field is **optional** and serves as a default value. Admin can:
  - Leave it empty (mahasiswa chooses from scratch during proposal)
  - Set it to `"Magang"` or `"Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)"` (pre-filled as default)
- **Mahasiswa chooses jenis in proposal:** When mahasiswa submits their PKL proposal via `/mahasiswa/proposal`, they **must** choose from:
  - `"Magang"` — Regular internship
  - `"Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)"` — Special programs (MSIB, MBKM, etc.)
  - If admin pre-set `users.jenis`, it appears as the default selection (mahasiswa can override)
- **Authoritative source:** `proposal_mahasiswas.jns_pkl` is the definitive PKL type after proposal submission.
- **Data hierarchy:** 
  - `users.jenis` = Optional default value (set by admin)
  - `proposal.jns_pkl` = Final authoritative value (chosen by mahasiswa, may differ from user.jenis)
- **File naming:** Uploaded documents (laporan, etc.) use simplified jenis from proposal: "Magang", "PMK", or "PKL" (default).

**Important for developers:**
- Query by proposal jenis using `ProposalMahasiswa::magang()` or `ProposalMahasiswa::msib()` scopes
- `users.jenis` is optional and serves as default value only — DO NOT use for filtering or business logic
- Display views load `proposalMahasiswa` relationship and show `jns_pkl` (authoritative)
- Admin user list shows `proposal.jns_pkl` for mahasiswa with proposals, fallback to `users.jenis` for display purposes
- Import system accepts optional jenis column (admin can pre-set defaults for bulk import)

### Mahasiswa Proposal & Laporan Flow

**Mahasiswa proposal ownership uses NIM/username as the natural key.**

- Use `ProposalMahasiswa::where('nim', $user->username)` for mahasiswa-facing proposal, dashboard, laporan, and file access flows.
- Do not rely on `proposal_mahasiswas.user_id` for mahasiswa ownership checks in controllers or file authorization. Existing/imported data may have correct `nim` while `user_id` is stale.
- Request identity fields (`nim`, `nama`, `kd_lokal`) are not trusted from browser input. `StoreProposalRequest` and `UpdateProposalRequest` force them from the authenticated user before validation; `ProposalService` also persists identity from the authenticated user.
- Mahasiswa can update proposal/laporan only while not graded (`nilai` not greater than 0).
- Upload laporan and reset laporan both require the laporan period to be open (`OpeningHour::isLaporanBuka()`).
- Upload laporan requires at least one file among `lp`, `lpp`, or `skp`; empty uploads must fail validation.
- Mentor accounts are auto-created/synced from proposal mentor data. New mentor default password intentionally uses `hp_mentor`; existing mentor passwords must not be changed silently.

### Dosen PA Flow

**Dosen PA ownership is strict NIP/username based. Do not fall back to dosen names.**

- `users.nama_dosen_pa` stores the Dosen PA username/NIP for mahasiswa users.
- `proposal_mahasiswas.dosen_pa` stores the Dosen PA username/NIP for proposals.
- Dosen-facing proposal/list/detail/nilai/export queries must scope by authenticated dosen username/NIP.
- Dosen detail modals should resolve proposal data by mahasiswa NIM/username when needed; do not assume `proposal_mahasiswas.user_id` is always reliable.
- Dosen can input nilai even if mahasiswa document uploads are incomplete, matching legacy v1 behavior.
- Dosen scoped CSV exports live on separate pages:
  - `/dosen/exports/pkl` (`dosen.exports.pkl.index`)
  - `/dosen/exports/msib` (`dosen.exports.msib.index`)
- Dosen PDF routes remain available for cetak/rekap behavior; do not remove unless explicitly requested.

### Mentor Flow

**Mentor ownership is scoped by mentor email.**

- Mentor-facing proposal/list/detail/nilai/PDF queries must scope by authenticated mentor `email` against `proposal_mahasiswas.email_mentor`.
- Mentor PKL/MSIB pages are intentionally merged into unified pages:
  - `/mentor/mahasiswa` (`mentor.mahasiswa.index`)
  - `/mentor/nilai` (`mentor.nilai.index`)
- Old mentor PKL/MSIB URLs redirect to the unified pages for usability.
- Mentor can input nilai even if mahasiswa document uploads are incomplete, matching legacy v1 behavior.
- Mentor export routes are intentionally disabled. Mentor can still use PDF cetak rekap routes.
- Mentor detail modals should show active document fields only: `skm`, `lp`, `lpp`, `skp`. The `proposal` file field is legacy/unused in the active flow.

### Export & PDF Rules

- Admin uses Export Data for downloads; old admin Data Mahasiswa PDF buttons/routes are removed.
- Admin export generation must use server-side filters saved on the `exports` record, not browser-submitted row data.
- CSV output must guard against formula injection for values starting with `=`, `+`, `-`, or `@`.
- Public export short-code downloads are intentionally accessible for external sharing, but must stay rate-limited.
- `exports.short_code` length is 10; keep generated codes at 10 characters.
- Dosen export pages use scoped exports; mentor export pages/routes should not exist.
- Dosen and mentor PDF rekap/cetak routes remain separate from CSV export behavior.

### Services Layer

Business logic is intentionally separated from controllers. Add domain logic here before bloating controllers:

- `DashboardService` — dashboard stats with caching
- `ProposalService` — proposal CRUD
- `NilaiService` — grade management
- `MahasiswaService` — student operations
- `DataMahasiswaService` — student data operations
- `UserService` — user management
- `ImportService` — Excel import
- `PdfService` — PDF generation
- `FonnteService` — WhatsApp integration
- `GroqService` — AI integration

### Model Scopes (`ProposalMahasiswa`)

Frequently used query scopes:

- `belumDinilai()` — `nilai` is null or 0
- `sudahDinilai()` — `nilai` > 0
- `magang()` — `jns_pkl` == `'Magang'`
- `msib()` — `jns_pkl` != `'Magang'` or contains PMK/MSIB
- `byDosen($nipDosen)` — via `user.nama_dosen_pa` (stores Dosen PA username/NIP)
- `byMentor($emailMentor)` — via `email_mentor`

## Frontend

- **Entry:** `resources/css/app.css`, `resources/js/app.js`
- **Libraries:** Chart.js (with Moment adapter), Flatpickr, Alpine.js plugins (anchor, collapse, focus, intersect, morph, persist)

## Testing & Seeding

- **PHPUnit config:** SQLite `:memory:` in testing env (`phpunit.xml`)
- **Default seeds** (`DatabaseSeeder`):
  - Admin: `admin` / `admin123`
  - Dosen: `1234567890` / `dosen123`
  - Mahasiswa: `12345678` / `mhs123`
  - Mentor: `sari@perusahaan.com` / `mentor123`
  - Plus one `OpeningHour` record with default academic timeline
- **Dummy data:** `DummyDataSeeder` creates 10 extra sample mahasiswa with proposals

## Security & Caching

- **Middleware:** `SecurityHeaders`, `DdosProtection` (rate limiting), `CheckRole`, `CheckOtpVerified`
- **Cache TTLs:** Dashboard stats 60s, lists 120s, recent logs 30s
- **Scheduled command:** `pkl:check-timeline` runs daily at `08:00` — broadcasts PKL opening emails, sends H-3 reminder emails to unregistered students, and auto-fills `nilai=75` for unscored proposals after `close_nilai` date (all based on `OpeningHour` settings)
