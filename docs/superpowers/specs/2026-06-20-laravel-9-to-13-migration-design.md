# IITC API — Laravel 9 → Laravel 13 Migration Design

**Date:** 2026-06-20
**Status:** Approved (pending spec review)
**Author:** Migration planning session

---

## 1. Goal

Migrate the IITC API from **Laravel 9.19 (EOL Feb 2024, security-vulnerable) on PHP ^8.0.2** to **Laravel 13 (current) on PHP 8.3+**, so that:

1. The security vulnerabilities of the EOL framework are eliminated.
2. The project installs and boots on the developer's local machine (PHP 8.5.3, Composer 2.9.2, Node 25) — currently blocked because Laravel 9's pinned dependencies do not declare PHP 8.5 compatibility.
3. The API surface (endpoints, request/response shapes) remains identical to the current behavior.

## 2. Constraints & scope

| Constraint | Decision |
|---|---|
| Target framework | Laravel **13.x** (`^13.0`) |
| Target PHP | **8.3+** (local machine runs 8.5.3 ✓) |
| Strategy | **Fresh skeleton + port code** (Approach A) |
| Data | **Disposable** — `migrate:fresh --seed` is acceptable; no data-preservation tooling needed |
| Frontend | **API-only** — drop Breeze/Vite/Tailwind scaffolding; keep the Mail layer |
| DB driver | **MySQL** (preserved from current `.env.example`) |
| Admin functionality | **Preserve** — role-based access control (Spatie roles + policies + Super Admin gate), NOT a panel package |

**Out of scope:** schema/data migration tooling, new features, frontend redesign, CI/CD changes, production deployment automation.

## 3. Current state (inventory)

- ~15 models, ~30 controllers, 24 migrations
- Custom: `App\Traits\Mutator\HashingPassword` (Laravel `Attribute` cast — still valid in L13), const-based helper classes (`App\Helpers\*` — plain classes, no migration impact)
- 3 Mailables: `SendSeminarParticipantTicket`, `SendSeminarParticipantTicketFail`, `SendsPasswordResetEmails` (Blade templates in `resources/views/mails/`)
- Auth: API-based (Sanctum) — `LoginController`, `RegisterController`, `VerifyEmailController` (signed URL), `PasswordResetLinkController`, `NewPasswordController`
- Admin/RBAC: `Admin\TeamController`, `AdminGetDetailTeamController`, 7 policy classes, `Super Admin` `Gate::before` bypass in `AuthServiceProvider`, roles assigned via `$user->assignRole('User')` in `RegisterController`
- `EventServiceProvider` has only the framework-default `Registered` → `SendEmailVerificationNotification` listener
- `AppServiceProvider` is empty
- Migrations use `->change()` in 2 files — handled natively by Laravel 9+; `doctrine/dbal` is removable

## 4. Target environment & toolchain

- PHP 8.5.3 (local) — satisfies L13's 8.3 floor
- Composer 2.9.2
- Node 25 (only needed if we keep Vite — we're dropping it, so Node becomes optional)
- MySQL (unchanged)

## 5. Package version map

| Package | Current (L9) | Target (L13) | Notes |
|---|---|---|---|
| `php` | `^8.0.2` | `^8.3` | floor bump |
| `laravel/framework` | `^9.19` | `^13.0` | core |
| `laravel/sanctum` | `^3.0` | `^4.0` | API tokens |
| `laravel/telescope` | `^5.2` | latest `^5.x` (dev) | install via `telescope:install` |
| `laravel/tinker` | `^2.7` | latest `^2.x` | bump |
| `spatie/laravel-permission` | `^5.10` | **`^8.0`** | confirmed: v8.0.0 (2026-05-30) declares `php: ^8.3`, `illuminate/*: ^12.0|^13.0`; biggest breaking-change item |
| `cviebrock/eloquent-sluggable` | `^9.0` | latest L13-compatible (`^10.x`/`^11.x`) | verify `Sluggable` trait still applies on models |
| `doctrine/dbal` | `^3.0` | **remove** | Laravel 9+ handles `->change()` natively |
| `sentry/sentry-laravel` | `^3.6` | latest `^4.x` | bump |
| `predis/predis` | `^2.2` | `^2.2`/`^3.0` | verify |
| `guzzlehttp/guzzle` | `^7.2` | `^7.5` | bump |
| `laravel/breeze` | `^1.19` (dev) | **remove** | frontend scaffolding, dropped |
| `laravel/sail` | `^1.0.1` (dev) | keep optional (dev) | decide at install |
| `phpunit/phpunit` | `^9.5.10` | `^11.x`/`^12.x` (dev) | test toolchain |
| `nunomaduro/collision` | `^6.1` | latest (dev) | bump |
| `spatie/laravel-ignition` | `^1.0` | `^2.x` (dev) | dev error pages |
| `laravel/pint` | `^1.0` | `^1.x` (dev) | bump |
| `fakerphp/faker` | `^1.9.1` | `^1.23` (dev) | bump |
| `mockery/mockery` | `^1.4.4` | `^1.6` (dev) | bump |

**Resolution policy:** Where a target version is uncertain, the implementation step runs `composer require <pkg>` without a constraint first, letting Composer pick the latest L13-compatible release, then pins the resolved version.

## 6. Breaking changes anticipated (from code inspection)

1. **`config/app.php` overhaul** — current file lists ~22 explicit framework providers; L13's skeleton has a bare `config/app.php` (providers auto-discovered via `bootstrap/providers.php`). Do NOT copy the old file verbatim. Merge only custom keys: `name`, `env`, `debug`, `url`, `web_url` (custom), `asset_url`, `timezone` (`Asia/Jakarta`), `locale` (`id`), `fallback_locale`, `faker_locale`, `key`, `cipher`, `maintenance`, `aliases`.
2. **`RouteServiceProvider` removal** — L11+ has no `RouteServiceProvider`; the `/api` prefix + `api` middleware come from `bootstrap/app.php` + auto-loaded `routes/api.php` (via `install:api`). Recreate the `api` rate limiter (`Limit::perMinute(60)`) in `bootstrap/app.php`.
3. **`spatie/laravel-permission` 5 → 8** — re-publish vendor config (`permission.php`) and migration; verify `HasRoles` trait on `User` still works; verify the permission tables migration (`2024_08_11_143851_add_event_permission.php`, `2024_08_11_144053_add_event_iitc_2023.php` seeders). Target `^8.0` (v8.0.0 released 2026-05-30, declares `php ^8.3` + `illuminate ^12|^13`).
4. **`AuthServiceProvider` → modern structure** — L13 auto-discovers policies; the `Super Admin` `Gate::before` bypass moves to `AppServiceProvider::boot()`. Drop the explicit `$policies` array (auto-discovery replaces it). See §7.
5. **`EventServiceProvider`** — the only listener (`Registered` → `SendEmailVerificationNotification`) is framework-default behavior; can be dropped or recreated in `AppServiceProvider`/`EventServiceProvider` if desired.
6. **`HashingPassword` trait** — uses `Attribute::make(set:)`, still valid in L13. Verify only.
7. **`doctrine/dbal` removal** — verify the 2 `->change()` migrations run without dbal (they will — Laravel 9+ native).
8. **Sanctum 3 → 4** — minor config/middleware changes; re-publish `sanctum.php` if needed.
9. **Telescope** — re-install via `php artisan telescope:install`; republish config.
10. **Sentry 3 → 4** — provider registration and config format changes; re-publish `sentry.php`.
11. **Carbon / minor API shifts** — spot-fix on first boot (e.g., `Carbon::parse` return typing).

## 7. Admin / RBAC preservation plan

The app has **no admin panel package**. "Admin" = role-based access control on the JSON API:

- **Roles:** `User` (assigned at registration), `Admin` (checked via `hasRole('Admin')` in controllers), `Super Admin` (granted all permissions via gate)
- **Controllers to port verbatim:** `Admin\TeamController`, `AdminGetDetailTeamController`
- **7 policy classes** (`app/Policies/`) — port verbatim; L13 auto-discovers them (no manual `$policies` registration)
- **`Super Admin` gate bypass** — currently `Gate::before` in `AuthServiceProvider`; **moves to `AppServiceProvider::boot()`** in L13
- **Spatie permission tables** — re-publish migration + config as part of Spatie re-install

**Decision:** Do NOT preserve `AuthServiceProvider` as a distinct file. Port its two concerns (policy map → auto-discovery; `Gate::before` → `AppServiceProvider`) to match the modern skeleton. The `Admin\TeamController` and all policies keep their file locations and contents.

## 8. What gets ported vs. regenerated

| **Port verbatim** (business logic) | **Regenerate / adopt from new skeleton** |
|---|---|
| `app/Models/*` (15 files) | `composer.json` (rewrite constraints) |
| `app/Http/Controllers/*` (incl. `Admin/`) | `config/*` (start from L13 defaults, merge custom keys) |
| `app/Mail/*` (3 Mailables) | `bootstrap/app.php`, `bootstrap/providers.php` |
| `app/Policies/*` (7 files) | `public/index.php`, `artisan` |
| `app/Traits/*`, `app/Helpers/*` | `package.json` (slim, no Vite/Tailwind/Breeze) |
| `database/migrations/*` (24 files) | `phpunit.xml`, `tests/TestCase.php`, `tests/CreatesApplication.php` |
| `routes/api.php`, `routes/auth.php`, `routes/console.php`, `routes/channels.php` | `.env.example` (L13 format + custom keys) |
| `resources/views/mails/*` (Blade email templates) | `app/Providers/AppServiceProvider.php` (add `Gate::before`) |

## 9. High-level execution order

(Detailed step-by-step plan will be produced by the writing-plans skill after spec approval.)

1. Create migration branch `chore/upgrade-laravel-13`
2. Back up current repo state (tag/commit point)
3. Scaffold fresh Laravel 13 API skeleton (no Breeze) via `laravel new` into a temp sibling directory, then copy **only the skeleton files** (`artisan`, `public/index.php`, `bootstrap/app.php`, `bootstrap/providers.php`, `config/*`, `composer.json`, `package.json`, `phpunit.xml`, `tests/TestCase.php`, `tests/CreatesApplication.php`) into the repo — leaving `app/`, `routes/`, `database/`, `resources/views/mails/` untouched for the port steps below
4. Port `app/Models`, `app/Http/Controllers`, `app/Mail`, `app/Policies`, `app/Traits`, `app/Helpers`
5. Port `database/migrations`, `database/seeders`, `database/factories`
6. Port `routes/api.php`, `routes/auth.php`, `routes/console.php`, `routes/channels.php`; wire rate limiter in `bootstrap/app.php`
7. Port `resources/views/mails/*`; verify Blade compiles
8. Rewrite `composer.json` with the version map (§5); `composer install`
9. Build `config/app.php` (merge custom keys), `bootstrap/app.php`, `bootstrap/providers.php`
10. Re-install Spatie permission (`vendor:publish` config + migration); re-install Telescope (`telescope:install`); re-publish Sentry config
11. Move `Super Admin` `Gate::before` into `AppServiceProvider::boot()`
12. `php artisan key:generate`; `php artisan migrate:fresh --seed`
13. Boot server, hit `/api` health route (`'ok! @iitc'`)
14. Smoke-test endpoint groups: auth (login/register/verify), competitions, teams + admin teams, payments, seminar
15. Run test suite (`php artisan test`)
16. Commit in logical chunks (skeleton / port / config / verify)

## 10. Verification / acceptance criteria

- [ ] `composer install` completes with no errors on PHP 8.5
- [ ] `php artisan key:generate`, `migrate:fresh --seed` succeed
- [ ] `php artisan serve` boots; `GET /api` returns `'ok! @iitc'`
- [ ] `GET /api/competitions` returns 200 with expected JSON shape (unauthenticated, public route)
- [ ] `POST /api/register` creates a user with `User` role and returns token
- [ ] `POST /api/login` returns Sanctum token; authenticated routes return 200
- [ ] Admin routes reject non-admin users (403) and accept `Admin` role users (200)
- [ ] `php artisan test` passes (or, if only stub tests exist, they pass)
- [ ] `php -v` shows 8.5.3; `composer show laravel/framework` shows `^13.0`
- [ ] No `doctrine/dbal` in `composer.lock`
- [ ] `Super Admin` gate bypass works (a Super Admin user can call any authorized route)

## 11. Risks & mitigations

| Risk | Likelihood | Mitigation |
|---|---|---|
| Spatie v8 has a behavioral regression vs v5 | Low | v8.0.0 stable since 2026-05-30; `HasRoles` trait API unchanged across majors; covered by smoke test of admin routes |
| A ported controller uses a deprecated L9 API | Medium | First-boot error-driven fix; test suite + smoke tests catch |
| Sluggable trait breaks on L13 | Low | Verify on `Competition` model first-boot; pin compatible version |
| Custom Blade mail templates reference removed directives | Low | Blade is stable; verify compile on boot |
| Sanctum token format change breaks existing clients | N/A | Data is disposable; tokens regenerated on re-seed |
| Migration order changes break `migrate:fresh` | Low | Migration timestamps preserved verbatim; order unchanged |
