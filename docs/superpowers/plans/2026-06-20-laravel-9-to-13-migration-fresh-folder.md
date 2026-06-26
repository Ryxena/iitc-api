# IITC API — Laravel 9 → Laravel 13 Migration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Migrate the IITC API from Laravel 9.19 (EOL, security-vulnerable, won't install on PHP 8.5) to Laravel 13 (current) on PHP 8.3+, so the project boots and runs identically on the developer's machine.

**Architecture:** User-scaffolded-skeleton-and-port strategy. **The user** creates a brand-new, clean Laravel 13 project at `E:\vs\iitc-api` (no Breeze, PHPUnit, fresh git repo). We then port the existing business code from `E:\vs\iitc-api-beta` (the old project) **into** the new project: models, controllers, routes, migrations, mail views, policies, helpers, traits. Admin/RBAC is preserved as roles + policies + a Super Admin gate (no panel package). The old `beta` folder is left untouched as a reference + safety net.

**Tech Stack:** Laravel 13, PHP 8.5 (local), Sanctum 4, Spatie laravel-permission 8, Sluggable, Telescope, Sentry 4, MySQL.

**Spec:** `docs/superpowers/specs/2026-06-20-laravel-9-to-13-migration-design.md`

**Source project:** `E:\vs\iitc-api-beta` (read-only reference — never modified)
**Target project:** `E:\vs\iitc-api` (user-created fresh Laravel 13 — port destination)

---

## How to use this plan

- **Two folders** are in play. `BETA` = `E:\vs\iitc-api-beta` (source, read-only). `NEW` = `E:\vs\iitc-api` (target).
- All commands assume `cmd.exe` on Windows; paths use backslashes.
- "Port verbatim" = copy a file from `BETA` to the same relative path in `NEW`, no edits.
- Verify after each task before committing. Do not skip verification steps.
- If a command fails, stop and read the error — do not push forward.

---

## Task 0: User scaffolds the fresh Laravel 13 project (prerequisite, done by the user)

This task is **performed by the user**, not the agent. The agent cannot proceed until the user reports it done.

**Target:** `E:\vs\iitc-api`

- [ ] **Step 1: User creates the fresh Laravel 13 project**

The user runs (from any directory):
```cmd
laravel new "E:\vs\iitc-api" --php
```
At the prompts, the user selects:
- Starter kit: **None** (no Breeze)
- Testing framework: **PHPUnit**
- Database: **mysql**
- Initialize git: **Yes**
- (Decline any other optional services for now)

Expected: a bootable Laravel 13 project at `E:\vs\iitc-api`, with its own git repo and initial commit.

- [ ] **Step 2: User verifies it boots**

The user runs:
```cmd
cd /d "E:\vs\iitc-api" && php artisan --version
```
Expected: `Laravel Framework 13.x.x`.

- [ ] **Step 3: User reports completion to the agent**

The agent proceeds only when the user confirms: "fresh L13 created at `E:\vs\iitc-api`, boots clean, git initialized."

---

## Task 1: Verify the fresh project and baseline it

**Files:** none in NEW yet (verification + git baseline).

- [ ] **Step 1: Confirm the target project is Laravel 13**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan --version
```
Expected: `Laravel Framework 13.x.x`. If this fails, stop — the prerequisite (Task 0) wasn't actually completed; ask the user.

- [ ] **Step 2: Confirm the source (beta) project is intact and readable**

Run:
```cmd
dir /B "E:\vs\iitc-api-beta\app\Models"
```
Expected: 15 model files (Category.php, Competition.php, ..., User.php). This confirms BETA is the source of truth.

- [ ] **Step 3: Confirm git is initialized in NEW**

Run:
```cmd
git -C "E:\vs\iitc-api" status
```
Expected: clean working tree (or an initial commit exists). If no git repo, run `git -C "E:\vs\iitc-api" init && git -C "E:\vs\iitc-api" add -A && git -C "E:\vs\iitc-api" commit -m "Initial: fresh Laravel 13 skeleton"`.

- [ ] **Step 4: Tag the baseline as a safety point**

Run:
```cmd
git -C "E:\vs\iitc-api" tag baseline-fresh-l13
```
Expected: no output (success). This is the rollback point to a clean L13 if porting goes wrong.

---

## Task 2: Add the migration dependencies to composer.json

The fresh project has L13's default dependencies. We add the IITC packages (Spatie permission, Sanctum, Telescope, Sluggable, Sentry, Predis, Guzzle).

**Files:**
- Modify: `E:\vs\iitc-api\composer.json`

- [ ] **Step 1: Require the IITC packages**

Run each (Composer updates composer.json and installs):
```cmd
cd /d "E:\vs\iitc-api" && composer require laravel/sanctum:^4.0
composer require spatie/laravel-permission:^8.0
composer require cviebrock/eloquent-sluggable
composer require sentry/sentry-laravel:^4.0
composer require laravel/telescope --dev
composer require predis/predis
composer require guzzlehttp/guzzle:^7.9
```
Expected: each `composer require` completes without error on PHP 8.5.

Notes:
- If `cviebrock/eloquent-sluggable` resolves to a version incompatible with L13, Composer will report it — then run `composer require cviebrock/eloquent-sluggable:^10.0 || ^11.0` and let it pick.
- Sanctum and Telescope are sometimes pre-included by the L13 installer; if a require says "already installed", that's fine.

- [ ] **Step 2: Verify all key packages at the right majors**

Run:
```cmd
cd /d "E:\vs\iitc-api" && composer show laravel/framework spatie/laravel-permission laravel/sanctum sentry/sentry-laravel cviebrock/eloquent-sluggable laravel/telescope
```
Expected: each shows the intended major version (`laravel/framework` 13.x, `spatie/laravel-permission` 8.x, `sanctum` 4.x, `sentry-laravel` 4.x, `telescope` 5.x).

- [ ] **Step 3: Verify the app still boots after adding packages**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan route:list
```
Expected: a route table (no fatal error). If this errors, fix before continuing.

- [ ] **Step 4: Commit**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "build: add IITC dependencies (Sanctum, Spatie, Sluggable, Sentry, Telescope)"
```

---

## Task 3: Port the business code from BETA to NEW (verbatim)

This is the core port. We copy every custom file from `BETA` to the same relative path in `NEW`. The fresh L13 skeleton's default `app/Models/User.php`, `app/Http/Controllers/Controller.php`, etc. get **overwritten** by the ported IITC versions.

**Files (port verbatim from `E:\vs\iitc-api-beta` → `E:\vs\iitc-api`):**
- `app\Models\*` (15 files) — overwrite the stub `User.php`
- `app\Http\Controllers\*` incl. `Admin\` (30 files) — overwrite stub `Controller.php`
- `app\Mail\*` (3 files)
- `app\Policies\*` (9 files)
- `app\Traits\Mutator\HashingPassword.php`
- `app\Helpers\*` (4 files)
- `database\migrations\*` (24 files)
- `database\seeders\*` (8 files) — overwrite stub `DatabaseSeeder.php`
- `database\factories\*` (10 files) — overwrite stub `UserFactory.php`
- `routes\api.php`, `routes\console.php`, `routes\channels.php` (NOT `auth.php` — it's an unreferenced Breeze leftover in BETA; nothing loads it)
- `resources\views\mails\*` (3 Blade files)

- [ ] **Step 1: Port app\ (models, controllers, mail, policies, traits, helpers)**

Run:
```cmd
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Models" "E:\vs\iitc-api\app\Models"
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Http\Controllers" "E:\vs\iitc-api\app\Http\Controllers"
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Mail" "E:\vs\iitc-api\app\Mail"
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Policies" "E:\vs\iitc-api\app\Policies"
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Traits" "E:\vs\iitc-api\app\Traits"
xcopy /E /I /Y "E:\vs\iitc-api-beta\app\Helpers" "E:\vs\iitc-api\app\Helpers"
```
Expected: each prints a count of files copied. The stub `User.php`, `Controller.php` in NEW are overwritten by IITC versions.

- [ ] **Step 2: Port database\ (migrations, seeders, factories)**

Run:
```cmd
xcopy /E /I /Y "E:\vs\iitc-api-beta\database\migrations" "E:\vs\iitc-api\database\migrations"
xcopy /E /I /Y "E:\vs\iitc-api-beta\database\seeders" "E:\vs\iitc-api\database\seeders"
xcopy /E /I /Y "E:\vs\iitc-api-beta\database\factories" "E:\vs\iitc-api\database\factories"
```
Expected: 24 migrations, 8 seeders, 10 factories copied. The stub `DatabaseSeeder.php` and `UserFactory.php` in NEW are overwritten.

- [ ] **Step 3: Port routes\**

Run:
```cmd
xcopy /E /I /Y "E:\vs\iitc-api-beta\routes" "E:\vs\iitc-api\routes"
```
Expected: `api.php`, `console.php`, `channels.php`, `web.php` copied. (The fresh L13 `web.php` gets overwritten by BETA's — which has Breeze leftover routes; we'll trim it in Task 11. We intentionally skip `routes\auth.php` — it's an unreferenced Breeze file in BETA.)

To avoid copying the dead `auth.php`, delete it after the xcopy if it came over:
```cmd
del "E:\vs\iitc-api\routes\auth.php" 2>nul
```

- [ ] **Step 4: Port the mail Blade views**

Run:
```cmd
xcopy /E /I /Y "E:\vs\iitc-api-beta\resources\views\mails" "E:\vs\iitc-api\resources\views\mails"
```
Expected: 3 Blade files copied (`send_password_reset_email.blade.php`, `send_seminar_ticket.blade.php`, `send_seminar_ticket_fail.blade.php`).

- [ ] **Step 5: Verify the IITC health route survived the port**

Run:
```cmd
findstr "ok! @iitc" "E:\vs\iitc-api\routes\api.php"
```
Expected: a match (the `Route::get('', fn () => 'ok! @iitc');` line).

- [ ] **Step 6: Commit the port**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "feat: port IITC business code (models, controllers, mail, policies, migrations, routes)"
```
Expected: a large commit listing all ported files.

---

## Task 4: Wire bootstrap/app.php for the API

L13's default `bootstrap/app.php` only loads `web.php` routes. We add the `api.php` routes (with the `/api` prefix and `api` middleware group auto-applied), the Sanctum stateful middleware, and recreate the API rate limiter from the deleted BETA `RouteServiceProvider`.

**Files:**
- Modify: `E:\vs\iitc-api\bootstrap\app.php`

- [ ] **Step 1: Overwrite bootstrap/app.php**

Replace the entire contents of `E:\vs\iitc-api\bootstrap\app.php` with:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        // `api:` auto-registers the 'api' prefix + 'api' middleware group
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum stateful API (matches original IITC behavior)
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// API rate limiter (replaces the deleted BETA RouteServiceProvider RateLimiter::for('api'))
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});
```

Note: `withRouting(api: ...)` replaces the deleted `RouteServiceProvider`. The `throttle:api` references in `routes/api.php` resolve to this limiter.

- [ ] **Step 2: Commit**

```cmd
git -C "E:\vs\iitc-api" add bootstrap\app.php
git -C "E:\vs\iitc-api" commit -m "feat: configure bootstrap/app.php for API routing + rate limiter"
```

---

## Task 5: Register the Super Admin gate in AppServiceProvider

The ported IITC controllers and policies rely on a `Gate::before` that grants the `Super Admin` role all permissions. In BETA this lived in the now-deleted `AuthServiceProvider`. We add it to the L13 `AppServiceProvider`.

**Files:**
- Modify: `E:\vs\iitc-api\app\Providers\AppServiceProvider.php`

- [ ] **Step 1: Overwrite AppServiceProvider.php**

Replace the entire contents of `E:\vs\iitc-api\app\Providers\AppServiceProvider.php` with:

```php
<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions.
        // Ported from the BETA AuthServiceProvider (no AuthServiceProvider in L13).
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
```

Note: The 9 policy classes in `app\Policies/` are **auto-discovered** by L13 (convention: `App\Models\Foo` → `App\Policies\FooPolicy`), so no explicit policy registration is needed. The framework-default `Registered` → `SendEmailVerificationNotification` listener is built into Laravel — no action needed.

- [ ] **Step 2: Commit**

```cmd
git -C "E:\vs\iitc-api" add app\Providers\AppServiceProvider.php
git -C "E:\vs\iitc-api" commit -m "feat: register Super Admin gate bypass in AppServiceProvider"
```

---

## Task 6: Apply IITC config customizations

The fresh L13 `config/app.php` has defaults (UTC, en). We restore the IITC values: `Asia/Jakarta` timezone, `id` locale, custom `web_url` key.

**Files:**
- Modify: `E:\vs\iitc-api\config\app.php`

- [ ] **Step 1: Set timezone to Asia/Jakarta**

In `E:\vs\iitc-api\config\app.php`, find `'timezone'` and set:
```php
'timezone' => 'Asia/Jakarta',
```

- [ ] **Step 2: Set locale to id, fallback en**

In the same file set:
```php
'locale' => 'id',
'fallback_locale' => 'en',
```

- [ ] **Step 3: Add the custom web_url key**

Immediately after the `'url'` line, add:
```php
'url' => env('APP_URL', 'http://localhost'),
'web_url' => env('APP_WEB_URL'),
'asset_url' => env('ASSET_URL'),
```

- [ ] **Step 4: Commit**

```cmd
git -C "E:\vs\iitc-api" add config\app.php
git -C "E:\vs\iitc-api" commit -m "feat: apply IITC config (timezone, locale, web_url)"
```

---

## Task 7: Publish Spatie permission config + migration

`spatie/laravel-permission ^8.0` needs its config and base migration published so the roles/permissions tables exist.

**Files:**
- Create: `E:\vs\iitc-api\config\permission.php`
- Create: a permission-tables migration in `E:\vs\iitc-api\database\migrations\`

- [ ] **Step 1: Publish Spatie permission config**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-config"
```
Expected: `config/permission.php` created.

- [ ] **Step 2: Publish Spatie permission migrations**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations"
```
Expected: a new file like `database\migrations\YYYY_MM_DD_000000_create_permission_tables.php` created.

- [ ] **Step 3: Commit**

```cmd
git -C "E:\vs\iitc-api" add config\permission.php database\migrations
git -C "E:\vs\iitc-api" commit -m "feat: publish spatie/laravel-permission config + migration"
```

---

## Task 8: Install Telescope stubs

Telescope needs its provider + config + stubs published. The ported `app/Providers/TelescopeServiceProvider.php` (from Task 3) is used; we run the installer to publish config and verify the provider is registered.

**Files:**
- Create: `E:\vs\iitc-api\config\telescope.php`

- [ ] **Step 1: Run telescope:install**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan telescope:install
```
Expected: `Telescope scaffolding installed successfully.` and `config/telescope.php` created.

If it asks to overwrite the existing `TelescopeServiceProvider.php` (ported in Task 3), **choose no** — we want to keep the IITC version with its `viewTelescope` gate.

- [ ] **Step 2: Verify the Telescope gate survived**

Run:
```cmd
findstr "viewTelescope" "E:\vs\iitc-api\app\Providers\TelescopeServiceProvider.php"
```
Expected: a match (the gate definition).

- [ ] **Step 3: Register the TelescopeServiceProvider in bootstrap/providers.php**

Check if it's already there:
```cmd
findstr "TelescopeServiceProvider" "E:\vs\iitc-api\bootstrap\providers.php"
```
If no match, add it. Edit `E:\vs\iitc-api\bootstrap\providers.php` to read:
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
];
```
(Spatie's provider is auto-discovered by Laravel — it does NOT need to be listed here.)

- [ ] **Step 4: Commit**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "feat: install Telescope + register provider"
```

---

## Task 9: Publish Sentry config

Sentry 4 needs its config published.

**Files:**
- Create: `E:\vs\iitc-api\config\sentry.php`

- [ ] **Step 1: Publish Sentry config**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```
Expected: `config/sentry.php` created.

- [ ] **Step 2: Verify**

Run:
```cmd
findstr "dsn" "E:\vs\iitc-api\config\sentry.php"
```
Expected: a line referencing the Sentry DSN env var.

- [ ] **Step 3: Commit**

```cmd
git -C "E:\vs\iitc-api" add config\sentry.php
git -C "E:\vs\iitc-api" commit -m "feat: publish Sentry 4 config"
```

---

## Task 10: Set up the .env file

We configure the `.env` with the IITC values (MySQL, mail, app name). The fresh project already has a `.env` from scaffolding; we edit it.

**Files:**
- Modify: `E:\vs\iitc-api\.env`

- [ ] **Step 1: Set the APP_ keys and DB keys**

Edit `E:\vs\iitc-api\.env` to set:
```env
APP_NAME=IITC
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_WEB_URL=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iitc
DB_USERNAME=root
DB_PASSWORD=
```
(Adjust DB_USERNAME/DB_PASSWORD for your local MySQL. Create the `iitc` database: `mysql -u root -e "CREATE DATABASE iitc;"` or via your DB tool.)

- [ ] **Step 2: Generate the app key**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan key:generate
```
Expected: `Application key set successfully.`

- [ ] **Step 3: Add APP_WEB_URL to .env.example (documentation)**

In `E:\vs\iitc-api\.env.example`, add after the `APP_URL=` line:
```env
APP_WEB_URL=
```

- [ ] **Step 4: Commit .env.example only (.env is gitignored)**

```cmd
git -C "E:\vs\iitc-api" add .env.example
git -C "E:\vs\iitc-api" commit -m "chore: document APP_WEB_URL in .env.example"
```

---

## Task 11: First boot — resolve breakages

This is the diagnostic task. We attempt to boot and fix whatever breaks. The ported code targets L9; some APIs shifted in L13.

**Files:** as-needed, discovered by errors.

- [ ] **Step 1: Clear any caches and dump autoload**

Run:
```cmd
cd /d "E:\vs\iitc-api" && composer dump-autoload && php artisan optimize:clear
```
Expected: autoload regenerated, caches cleared.

- [ ] **Step 2: Test that the app bootstraps (route listing)**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan route:list --path=api
```
Expected: a table of API routes (login, register, competitions, teams, admin/teams, etc.).

**Common breakage fixes:**

- **`Class App\Http\Controllers\X not found`** → run `composer dump-autoload` again (autoload dev).
- **`Spatie\Permission\Traits\HasRoles` not found** → run `composer show spatie/laravel-permission`; must be 8.x. The import in `app\Models\User.php` (`use Spatie\Permission\Traits\HasRoles;`) is unchanged in v8.
- **`Cviebrock\EloquentSluggable\Sluggable` not found** → check `composer show cviebrock/eloquent-sluggable` resolved; if the namespace changed in the resolved version, update the `use` line in `app\Models\Competition.php`.
- **`method statefulApi not found`** → Sanctum not installed/configured; run `php artisan install:api` to wire Sanctum, then re-check `bootstrap/app.php`.
- **Carbon deprecation notices** → non-fatal; ignore.
- **`Route name 'verification.verify'` warnings** → defined in `routes/api.php` (ported); non-fatal.

- [ ] **Step 3: Commit any breakage fixes**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "fix: resolve Laravel 13 boot breakages"
```
(Skip the commit if nothing broke.)

---

## Task 12: Database — migrate:fresh and seed

With the app booting, we test the full database lifecycle.

**Files:** none (runtime).

- [ ] **Step 1: Create the MySQL database if not already present**

Run:
```cmd
mysql -u root -e "CREATE DATABASE IF NOT EXISTS iitc;"
```
(Adjust `-u root` to match your local MySQL user. If you used a different DB name in `.env`, use that here.)

- [ ] **Step 2: Run fresh migrations + seeders**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan migrate:fresh --seed
```
Expected: all migrations (app's 24 + Spatie permission tables + L13 defaults) run; seeders populate roles, permissions, and test users (`superadmin@gmail.com`, `admin@gmail.com`, `user@gmail.com`, `member@gmail.com`, `notmember@gmail.com`).

**Common breakage fixes:**

- **`Class Database\Seeders\X not found`** → `composer dump-autoload` (autoload dev); the ported seeders need to be indexed.
- **`Indirect modification of overloaded property`** in a factory → check the factory's definition method; usually a `$guarded`/mass-assignment issue.
- **Migration order errors** → the migration timestamps were preserved verbatim, so order is unchanged. If a migration references a column added later, that's a pre-existing bug in BETA — port it as-is and note it.

- [ ] **Step 3: Commit any seeder/migration fixes**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "fix: resolve migration/seed issues for Laravel 13"
```
(Skip if clean.)

---

## Task 13: Smoke test — serve and hit endpoints

We verify the API responds correctly for the key endpoint groups.

**Files:** none (runtime verification).

- [ ] **Step 1: Start the server**

Run (in its own terminal; leave running):
```cmd
cd /d "E:\vs\iitc-api" && php artisan serve --port=8000
```
Expected: `Server running on [http://127.0.0.1:8000]`.

- [ ] **Step 2: Hit the health route**

Run (new terminal):
```cmd
curl -s http://127.0.0.1:8000/api
```
Expected: `ok! @iitc`

- [ ] **Step 3: Hit the public competitions route**

Run:
```cmd
curl -s http://127.0.0.1:8000/api/competitions
```
Expected: JSON with `{"status":1,"message":...,"data":...}` shape, HTTP 200.

- [ ] **Step 4: Login as admin (Sanctum token)**

Run:
```cmd
curl -s -X POST http://127.0.0.1:8000/api/login -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"email\":\"admin@gmail.com\",\"password\":\"myPassword\"}"
```
Expected: JSON containing a `token`. Copy the token for Step 5–6. Password `myPassword` comes from the ported `DatabaseSeeder`.

- [ ] **Step 5: Test an authenticated route (with admin token)**

Run (replace `<TOKEN>`):
```cmd
curl -s -i http://127.0.0.1:8000/api/teams -H "Authorization: Bearer <TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 200 OK` and a JSON teams list.

- [ ] **Step 6: Test admin RBAC — admin allowed, regular user denied**

Login as `user@gmail.com` / `myPassword` to get a user token, then:
```cmd
curl -s -i http://127.0.0.1:8000/api/admin/teams -H "Authorization: Bearer <USER_TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 403 Forbidden` (the `hasRole('Admin')` check rejects it).

Then with the admin token:
```cmd
curl -s -i http://127.0.0.1:8000/api/admin/teams -H "Authorization: Bearer <ADMIN_TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 200 OK`.

- [ ] **Step 7: Stop the server**

`Ctrl+C` in the server terminal.

- [ ] **Step 8: Commit any fixes from smoke testing**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "fix: resolve smoke-test issues"
```
(Skip if clean.)

---

## Task 14: Run the test suite and clean up Breeze-era stubs

The fresh L13 project ships stub tests; BETA's tests include Breeze leftovers (`tests/Feature/Auth/`, `ProfileTest.php`) that reference removed views/controllers.

**Files:**
- Possibly delete: `E:\vs\iitc-api\tests\Feature\Auth\*`, `E:\vs\iitc-api\tests\Feature\ProfileTest.php`

- [ ] **Step 1: Run the tests**

Run:
```cmd
cd /d "E:\vs\iitc-api" && php artisan test
```
Expected: tests pass. If they fail on `tests/Feature/Auth/*` or `ProfileTest.php` (Breeze leftovers that reference Blade views we didn't port), delete them:
```cmd
rmdir /S /Q "E:\vs\iitc-api\tests\Feature\Auth"
del "E:\vs\iitc-api\tests\Feature\ProfileTest.php"
```
Then re-run `php artisan test`.

- [ ] **Step 2: Commit any test cleanup**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit -m "test: drop Breeze-era stub tests"
```
(Skip if tests passed as-is.)

---

## Task 15: Final verification against acceptance criteria

This is the done-gate. Walk through every checkbox from the spec §10.

**Files:** none.

- [ ] **Step 1: Verify PHP version**

Run:
```cmd
php -v
```
Expected: `PHP 8.5.3`.

- [ ] **Step 2: Verify Laravel + key packages**

Run:
```cmd
cd /d "E:\vs\iitc-api" && composer show laravel/framework spatie/laravel-permission laravel/sanctum sentry/sentry-laravel cviebrock/eloquent-sluggable laravel/telescope
```
Expected: framework 13.x, permission 8.x, sanctum 4.x, sentry-laravel 4.x, sluggable installed, telescope 5.x.

- [ ] **Step 3: Verify doctrine/dbal is absent (we dropped it)**

Run:
```cmd
cd /d "E:\vs\iitc-api" && composer show doctrine/dbal 2>nul && echo "STILL PRESENT - FAIL" || echo "REMOVED OK"
```
Expected: `REMOVED OK`.

- [ ] **Step 4: Walk the acceptance checklist (spec §10)**

- [ ] `composer install` completes with no errors on PHP 8.5 — done in Task 2
- [ ] `php artisan key:generate`, `migrate:fresh --seed` succeed — done in Task 12
- [ ] `php artisan serve` boots; `GET /api` returns `'ok! @iitc'` — Task 13 Step 2
- [ ] `GET /api/competitions` returns 200 — Task 13 Step 3
- [ ] `POST /api/register` works — optionally test directly: `curl -s -X POST http://127.0.0.1:8000/api/register -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"name\":\"x\",\"email\":\"x@x.com\",\"password\":\"password\",\"password_confirmation\":\"password\"}"`
- [ ] Admin routes reject non-admin (403) and accept admin (200) — Task 13 Step 6
- [ ] `php artisan test` passes — Task 14
- [ ] PHP 8.5.3, Laravel 13 — Step 1–2 above
- [ ] No `doctrine/dbal` in lock — Step 3 above
- [ ] Super Admin gate works — login as `superadmin@gmail.com` / `myPassword`; verify any authorized route returns 200

- [ ] **Step 5: Final commit + summary log**

```cmd
git -C "E:\vs\iitc-api" add -A
git -C "E:\vs\iitc-api" commit --allow-empty -m "chore: complete Laravel 13 migration verification"
git -C "E:\vs\iitc-api" log --oneline
```
Expected: a clean log of the migration commits. The `E:\vs\iitc-api-beta` folder is untouched and remains as the reference/safety net.

---

## Rollback (if everything went wrong)

To discard all porting work and return to the clean L13 baseline:
```cmd
git -C "E:\vs\iitc-api" reset --hard baseline-fresh-l13
```
The `E:\vs\iitc-api-beta` folder was never modified — it is the ultimate fallback.
