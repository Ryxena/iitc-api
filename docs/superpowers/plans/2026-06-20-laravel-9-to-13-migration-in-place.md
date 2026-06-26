# IITC API — Laravel 9 → Laravel 13 Migration (In-Place Overlay)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Migrate the IITC API from Laravel 9.19 (EOL, security-vulnerable, won't install on PHP 8.5) to Laravel 13 (current) on PHP 8.3+, so the project boots and runs identically on the developer's machine.

**Architecture:** **In-place overlay.** We upgrade the framework *inside* the existing `E:\vs\iitc-api-beta` repo, preserving all git history. A throwaway L13 skeleton is generated in a temp directory; only its **framework files** (`artisan`, `config/*`, `bootstrap/*`, `composer.json`, `public/index.php`, test bootstrap) are overlaid onto the repo. Your business code (`app/`, `routes/api.php`, `database/migrations/`, `resources/views/mails/`) is **never moved or copied** — it stays byte-for-byte identical. The old service-provider registration style is dissolved into L13's `bootstrap/app.php` + `bootstrap/providers.php` + `AppServiceProvider`. Admin/RBAC is preserved as roles + policies + a Super Admin gate (no panel package).

**Tech Stack:** Laravel 13, PHP 8.5 (local), Sanctum 4, Spatie laravel-permission 8, Sluggable, Telescope, Sentry 4, MySQL.

**Spec:** `docs/superpowers/specs/2026-06-20-laravel-9-to-13-migration-design.md`

**Working directory (unchanged):** `E:\vs\iitc-api-beta`

**Companion plan:** `docs/superpowers/plans/2026-06-20-laravel-9-to-13-migration-fresh-folder.md` (alternative strategy for comparison)

---

## How to use this plan

- All commands assume `cmd.exe` on Windows; paths use backslashes.
- Working directory is **always** `E:\vs\iitc-api-beta` unless a step says otherwise.
- "Port verbatim" means: the file already exists at the right path — no action needed, we just verify it is intact.
- Verify after each task before committing. Do not skip verification steps.
- If a command fails, stop and read the error — do not push forward.

---

## Task 0: Create migration branch and safety tag

**Files:** none (git only)

- [ ] **Step 1: Create and switch to a migration branch**

Run:
```cmd
git -C "E:\vs\iitc-api-beta" checkout -b chore/upgrade-laravel-13
```
Expected: `Switched to a new branch 'chore/upgrade-laravel-13'`.

- [ ] **Step 2: Tag the pre-migration state as a safety point**

Run:
```cmd
git -C "E:\vs\iitc-api-beta" tag pre-laravel-13-migration
```
Expected: no output (success). This is the rollback point. `git reset --hard pre-laravel-13-migration` restores exactly today's state.

---

## Task 1: Scaffold a fresh Laravel 13 skeleton in a temp directory

We generate a clean L13 app *outside* the repo so we can copy only its skeleton files. Your existing `app/`, `routes/`, `database/` are never touched by this task.

**Files:** none in the repo yet (temp directory work)

- [ ] **Step 1: Check the Laravel installer is available**

Run:
```cmd
laravel --version
```
Expected: a version line. If "not recognized", install it: `composer global require laravel/installer` then re-run. If unavailable, use the fallback in Step 2.

- [ ] **Step 2: Scaffold a fresh Laravel 13 app in a temp sibling dir**

Run:
```cmd
laravel new "E:\vs\iitc-api-l13-skeleton" --php --no-interaction
```
Fallback if `laravel new` is unavailable:
```cmd
composer create-project laravel/laravel "E:\vs\iitc-api-l13-skeleton" "13.*"
```
At any prompts: **decline Breeze**, pick **PHPUnit**, **mysql**.

Expected: a new directory `E:\vs\iitc-api-l13-skeleton` containing a fresh Laravel 13 app.

- [ ] **Step 3: Verify the skeleton is Laravel 13**

Run:
```cmd
cd /d "E:\vs\iitc-api-l13-skeleton" && php artisan --version
```
Expected: `Laravel Framework 13.x.x`. If this fails, fix the scaffold before copying anything.

- [ ] **Step 4: Smoke-test the skeleton boots and migrates**

Run:
```cmd
cd /d "E:\vs\iitc-api-l13-skeleton" && php -r "copy('.env.example','.env');" && php artisan key:generate && php artisan migrate --env=testing
```
Expected: key generated, migrations run against the testing SQLite DB without error. (We discard this DB; it only proves the skeleton is sound.)

- [ ] **Step 5: Commit nothing yet — this is prep**

Do not commit. The skeleton lives outside the repo.

---

## Task 2: Overlay skeleton files into the repo

We replace framework files in `E:\vs\iitc-api-beta` with the L13 versions. Your business code in `app/`, `routes/`, `database/`, `resources/views/mails/` is **not touched** by any copy operation.

**Files (copy from `E:\vs\iitc-api-l13-skeleton` → `E:\vs\iitc-api-beta`):**
- Overwrite: `artisan`, `public\index.php`, `composer.json`, `package.json`, `phpunit.xml`
- Overwrite: `bootstrap\app.php`
- Create: `bootstrap\providers.php`
- Overwrite entire dir: `config\`
- Overwrite: `tests\TestCase.php`, `tests\CreatesApplication.php`

- [ ] **Step 1: Copy the top-level framework files**

Run:
```cmd
copy /Y "E:\vs\iitc-api-l13-skeleton\artisan" "E:\vs\iitc-api-beta\artisan"
copy /Y "E:\vs\iitc-api-l13-skeleton\public\index.php" "E:\vs\iitc-api-beta\public\index.php"
copy /Y "E:\vs\iitc-api-l13-skeleton\composer.json" "E:\vs\iitc-api-beta\composer.json"
copy /Y "E:\vs\iitc-api-l13-skeleton\package.json" "E:\vs\iitc-api-beta\package.json"
copy /Y "E:\vs\iitc-api-l13-skeleton\phpunit.xml" "E:\vs\iitc-api-beta\phpunit.xml"
```
Expected: `1 file(s) copied` for each.

- [ ] **Step 2: Copy bootstrap files**

Run:
```cmd
copy /Y "E:\vs\iitc-api-l13-skeleton\bootstrap\app.php" "E:\vs\iitc-api-beta\bootstrap\app.php"
copy /Y "E:\vs\iitc-api-l13-skeleton\bootstrap\providers.php" "E:\vs\iitc-api-beta\bootstrap\providers.php"
```
Expected: `1 file(s) copied` for each.

- [ ] **Step 3: Replace the entire config directory with the L13 version**

Run:
```cmd
rmdir /S /Q "E:\vs\iitc-api-beta\config"
xcopy /E /I /Y "E:\vs\iitc-api-l13-skeleton\config" "E:\vs\iitc-api-beta\config"
```
Expected: `config` directory recreated with L13 default files (`app.php`, `auth.php`, `database.php`, `mail.php`, etc.). Note: `permission.php`, `sluggable.php`, `telescope.php`, `sentry.php` will be **absent** — they get re-published in later tasks. That is expected.

- [ ] **Step 3b: Remove Breeze-era test/asset artifacts from the repo**

Breeze-leftover files in the repo are no longer needed. Run:
```cmd
del "E:\vs\iitc-api-beta\postcss.config.js" 2>nul
del "E:\vs\iitc-api-beta\tailwind.config.js" 2>nul
del "E:\vs\iitc-api-beta\vite.config.js" 2>nul
rmdir /S /Q "E:\vs\iitc-api-beta\resources\css" 2>nul
rmdir /S /Q "E:\vs\iitc-api-beta\resources\js" 2>nul
rmdir /S /Q "E:\vs\iitc-api-beta\resources\views\auth" 2>nul
rmdir /S /Q "E:\vs\iitc-api-beta\resources\views\profile" 2>nul
del "E:\vs\iitc-api-beta\resources\views\dashboard.blade.php" 2>nul
del "E:\vs\iitc-api-beta\resources\views\welcome.blade.php" 2>nul
```
Expected: dead files removed. `2>nul` suppresses errors if a file is already gone. The Mail views (`resources/views/mails/`) are **kept** — those are real and used by your Mailables.

- [ ] **Step 4: Copy L13 test bootstrap**

Run:
```cmd
copy /Y "E:\vs\iitc-api-l13-skeleton\tests\TestCase.php" "E:\vs\iitc-api-beta\tests\TestCase.php"
copy /Y "E:\vs\iitc-api-l13-skeleton\tests\CreatesApplication.php" "E:\vs\iitc-api-beta\tests\CreatesApplication.php"
```
Expected: `1 file(s) copied` for each.

- [ ] **Step 5: Delete the legacy providers that L13 no longer uses**

L13's skeleton registers providers via `bootstrap/providers.php` + `bootstrap/app.php`. Run:
```cmd
del "E:\vs\iitc-api-beta\app\Providers\RouteServiceProvider.php"
del "E:\vs\iitc-api-beta\app\Providers\AuthServiceProvider.php"
del "E:\vs\iitc-api-beta\app\Providers\EventServiceProvider.php"
del "E:\vs\iitc-api-beta\app\Providers\BroadcastServiceProvider.php"
```
**Keep** `AppServiceProvider.php` and `TelescopeServiceProvider.php`.

Expected: each file deleted. Their concerns are re-homed in Task 5 and Task 7.

- [ ] **Step 6: Commit the skeleton overlay**

Run:
```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "chore: overlay Laravel 13 skeleton (replace framework files)"
```
Expected: a commit listing the overwritten skeleton files, the new `bootstrap/providers.php`, the removed Breeze assets, and the deleted providers.

---

## Task 3: Rewrite composer.json with the target dependency set

The copied skeleton `composer.json` has L13 defaults but is missing our packages and still references Breeze-era dev deps. We rewrite it to the spec's version map.

**Files:**
- Modify: `composer.json`

- [ ] **Step 1: Overwrite composer.json with the migration target**

Replace the entire contents of `E:\vs\iitc-api-beta\composer.json` with:

```json
{
    "name": "laravel/laravel",
    "type": "project",
    "description": "The Laravel Framework.",
    "keywords": ["framework", "laravel"],
    "license": "MIT",
    "require": {
        "php": "^8.3",
        "cviebrock/eloquent-sluggable": "^10.0",
        "guzzlehttp/guzzle": "^7.9",
        "laravel/framework": "^13.0",
        "laravel/sanctum": "^4.0",
        "laravel/telescope": "^5.0",
        "laravel/tinker": "^2.10",
        "predis/predis": "^2.0",
        "sentry/sentry-laravel": "^4.0",
        "spatie/laravel-permission": "^8.0"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.0",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.0",
        "phpunit/phpunit": "^11.0",
        "spatie/laravel-ignition": "^2.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

Key changes vs. the L9 original: `php ^8.3`, `laravel/framework ^13.0`, `sanctum ^4.0`, `spatie/laravel-permission ^8.0`, `eloquent-sluggable ^10.0`, `sentry ^4.0`, **dropped `doctrine/dbal`**, **dropped `laravel/breeze`**, PHPUnit `^11.0`.

- [ ] **Step 2: Remove the old vendor directory and composer lock**

Run:
```cmd
rmdir /S /Q "E:\vs\iitc-api-beta\vendor"
del "E:\vs\iitc-api-beta\composer.lock"
```
Expected: removed. We regenerate them clean.

- [ ] **Step 3: Run composer install**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && composer install
```
Expected: installation completes with no errors on PHP 8.5. If a constraint cannot resolve (e.g. `eloquent-sluggable ^10.0` does not exist), **loosen that one constraint to `^10.0 || ^11.0`** and re-run. Do NOT downgrade Laravel or PHP.

- [ ] **Step 4: Verify key packages installed at the right majors**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && composer show laravel/framework spatie/laravel-permission laravel/sanctum sentry/sentry-laravel cviebrock/eloquent-sluggable
```
Expected: `laravel/framework` 13.x, `spatie/laravel-permission` 8.x, `sanctum` 4.x, `sentry-laravel` 4.x, `sluggable` 10.x or 11.x.

- [ ] **Step 5: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add composer.json composer.lock
git -C "E:\vs\iitc-api-beta" commit -m "build: target Laravel 13 + PHP 8.3 dependency set"
```

---

## Task 4: Verify business code is intact (port verification)

Your `app/`, `routes/`, `database/` were never copied — they stayed in place. We verify nothing was lost during Task 2's config overwrite.

**Files:** none (verification).

- [ ] **Step 1: Verify all expected business files are present**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && dir /B app\Models app\Http\Controllers app\Mail app\Policies app\Traits app\Helpers
```
Expected counts: Models 15, Controllers (incl. Admin) ~30, Mail 3, Policies 9, Traits 1, Helpers 4. (Also verify migrations 24, seeders 8, factories 10.)

If any are missing, restore from the safety tag:
```cmd
git -C "E:\vs\iitc-api-beta" checkout pre-laravel-13-migration -- <missing-file-path>
```

- [ ] **Step 2: Verify the IITC health route survived**

Run:
```cmd
findstr "ok! @iitc" "E:\vs\iitc-api-beta\routes\api.php"
```
Expected: a match (the `Route::get('', fn () => 'ok! @iitc');` line).

- [ ] **Step 3: Commit (only if files were restored)**

If you restored anything:
```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "chore: restore business code integrity after overlay"
```
(Skip if nothing was missing.)

---

## Task 5: Build bootstrap/app.php with the API rate limiter

L13 has no `RouteServiceProvider`. The `/api` prefix, `api` middleware, and the rate limiter live in `bootstrap/app.php`.

**Files:**
- Modify: `bootstrap\app.php`

- [ ] **Step 1: Overwrite bootstrap/app.php**

Replace the entire contents of `E:\vs\iitc-api-beta\bootstrap\app.php` with:

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
        // `api:` auto-registers the 'api' prefix and 'api' middleware group
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

- [ ] **Step 2: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add bootstrap\app.php
git -C "E:\vs\iitc-api-beta" commit -m "feat: configure bootstrap/app.php with API routing + rate limiter"
```

---

## Task 6: Build bootstrap/providers.php

L13 auto-loads providers from `bootstrap/providers.php`.

**Files:**
- Modify: `bootstrap\providers.php`

- [ ] **Step 1: Overwrite bootstrap/providers.php**

Replace the entire contents of `E:\vs\iitc-api-beta\bootstrap\providers.php` with:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TelescopeServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];
```

Note: L13 auto-discovers framework providers, so we only list app + third-party. Sanctum's provider is auto-discovered. Spatie is listed because it sometimes needs explicit registration.

- [ ] **Step 2: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add bootstrap\providers.php
git -C "E:\vs\iitc-api-beta" commit -m "feat: register app + Spatie providers"
```

---

## Task 7: Move the Super Admin gate into AppServiceProvider

The old `AuthServiceProvider` had two concerns: a policy map (now auto-discovered) and a `Gate::before` granting `Super Admin` all permissions. The latter must be re-homed.

**Files:**
- Modify: `app\Providers\AppServiceProvider.php`

- [ ] **Step 1: Overwrite AppServiceProvider.php**

Replace the entire contents of `E:\vs\iitc-api-beta\app\Providers\AppServiceProvider.php` with:

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
        // Migrated from the deleted AuthServiceProvider.
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
```

Note: The 9 policy classes in `app\Policies/` are **auto-discovered** by L13 (convention: `App\Models\Foo` -> `App\Policies\FooPolicy`), so the old explicit `$policies` array is no longer needed. The framework-default `Registered` -> `SendEmailVerificationNotification` listener is built into Laravel — no action needed.

- [ ] **Step 2: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add app\Providers\AppServiceProvider.php
git -C "E:\vs\iitc-api-beta" commit -m "feat: move Super Admin gate bypass into AppServiceProvider"
```

---

## Task 8: Merge custom config keys into config/app.php

The L13 `config/app.php` (copied in Task 2) is the bare default. We restore the IITC customizations: `Asia/Jakarta` timezone, `id` locale, custom `web_url` key.

**Files:**
- Modify: `config\app.php`

- [ ] **Step 1: Set the timezone to Asia/Jakarta**

In `E:\vs\iitc-api-beta\config\app.php`, find `'timezone'` and set:
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
git -C "E:\vs\iitc-api-beta" add config\app.php
git -C "E:\vs\iitc-api-beta" commit -m "feat: restore IITC config (timezone, locale, web_url)"
```

---

## Task 9: Re-publish Spatie permission config + migration

**Files:**
- Create: `config\permission.php`
- Create: a permission-tables migration

- [ ] **Step 1: Publish the Spatie permission config**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-config"
```
Expected: `config/permission.php` created.

- [ ] **Step 2: Publish Spatie permission migrations**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="permission-migrations"
```
Expected: a new file like `database\migrations\YYYY_MM_DD_000000_create_permission_tables.php`.

- [ ] **Step 3: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add config\permission.php database\migrations
git -C "E:\vs\iitc-api-beta" commit -m "feat: publish spatie/laravel-permission config + migration"
```

---

## Task 10: Re-install Telescope

**Files:**
- Create: `config\telescope.php`

- [ ] **Step 1: Run telescope:install**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan telescope:install
```
Expected: `Telescope scaffolding installed successfully.` If it asks to overwrite the existing `TelescopeServiceProvider` (kept in Task 2), choose **no** — keep the IITC version with its `viewTelescope` gate.

- [ ] **Step 2: Verify the Telescope gate survived**

Run:
```cmd
findstr "viewTelescope" "E:\vs\iitc-api-beta\app\Providers\TelescopeServiceProvider.php"
```
Expected: a match.

- [ ] **Step 3: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "feat: reinstall Telescope for Laravel 13"
```

---

## Task 11: Re-publish Sentry config

**Files:**
- Create: `config\sentry.php`

- [ ] **Step 1: Publish Sentry config**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```
Expected: `config/sentry.php` created.

- [ ] **Step 2: Verify**

Run:
```cmd
findstr "dsn" "E:\vs\iitc-api-beta\config\sentry.php"
```
Expected: a line referencing the Sentry DSN env var.

- [ ] **Step 3: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add config\sentry.php
git -C "E:\vs\iitc-api-beta" commit -m "feat: publish Sentry 4 config"
```

---

## Task 12: Update .env.example with custom keys

**Files:**
- Modify: `.env.example`

- [ ] **Step 1: Check current state of .env.example**

Run:
```cmd
findstr "APP_WEB_URL DB_CONNECTION" "E:\vs\iitc-api-beta\.env.example"
```
If `APP_WEB_URL` is missing or `DB_CONNECTION` is not `mysql`, proceed to Step 2.

- [ ] **Step 2: Restore the IITC .env.example**

Replace the contents of `E:\vs\iitc-api-beta\.env.example` with:

```env
APP_NAME=IITC
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_WEB_URL=

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iitc
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

SENTRY_LARAVEL_DSN=
```

- [ ] **Step 3: Create a local .env from the example and generate a key**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && copy /Y .env.example .env && php artisan key:generate
```
Expected: `.env` created, `Application key set successfully.`

- [ ] **Step 4: Commit**

```cmd
git -C "E:\vs\iitc-api-beta" add .env.example
git -C "E:\vs\iitc-api-beta" commit -m "chore: restore IITC .env.example"
```

---

## Task 13: First boot — resolve breakages

This is the diagnostic task. We attempt to boot and fix whatever breaks.

**Files:** as-needed.

- [ ] **Step 1: Clear caches and dump autoload**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && composer dump-autoload && php artisan optimize:clear
```
Expected: autoload regenerated, caches cleared.

- [ ] **Step 2: Test that the app bootstraps (route listing)**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan route:list --path=api
```
Expected: a table of API routes. If this errors, read the error — common fixes below.

**Common breakage fixes:**

- **`Class ... not found`** -> run `composer dump-autoload`.
- **`Spatie\Permission` traits error** -> verify `composer show spatie/laravel-permission` is 8.x. The `HasRoles` trait import in `app\Models\User.php` (`use Spatie\Permission\Traits\HasRoles;`) is unchanged in v8.
- **`Sluggable` trait not found** -> verify `cviebrock/eloquent-sluggable` installed; check the `use Cviebrock\EloquentSluggable\Sluggable;` import in `app\Models\Competition.php` still resolves.
- **`method statefulApi not found`** -> Sanctum not wired; run `php artisan install:api`.
- **Carbon deprecation warnings** -> non-fatal; ignore.
- **`Route name 'verification.verify'` warnings** -> defined in `routes/api.php`; non-fatal.

- [ ] **Step 3: Commit any breakage fixes**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "fix: resolve Laravel 13 boot breakages"
```
(Skip if nothing broke.)

---

## Task 14: Database — migrate:fresh and seed

**Files:** none (runtime).

- [ ] **Step 1: Configure a local MySQL database**

Create an empty MySQL database and set it in `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD):
```cmd
mysql -u root -e "CREATE DATABASE IF NOT EXISTS iitc;"
```

- [ ] **Step 2: Run fresh migrations + seeders**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan migrate:fresh --seed
```
Expected: all migrations (app's 24 + Spatie permission tables + L13 defaults) run; seeders populate roles, permissions, and test users (`superadmin@gmail.com`, `admin@gmail.com`, `user@gmail.com`, etc.).

**Common breakage fixes:**
- **`Class Database\Seeders\X not found`** -> `composer dump-autoload`.
- **`Indirect modification of overloaded property`** -> usually a `$guarded = []` + mass-assignment issue in a factory; check the factory.

- [ ] **Step 3: Commit any seeder/migration fixes**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "fix: resolve migration/seed issues for Laravel 13"
```
(Skip if clean.)

---

## Task 15: Smoke test — serve and hit endpoints

**Files:** none (runtime verification).

- [ ] **Step 1: Start the server**

Run (own terminal; leave running):
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan serve --port=8000
```
Expected: `Server running on [http://127.0.0.1:8000]`.

- [ ] **Step 2: Hit the health route**

Run:
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
Expected: JSON containing a `token`. Save it for Step 5-6. Password `myPassword` comes from `DatabaseSeeder`.

- [ ] **Step 5: Test an authenticated route**

Run (replace `<TOKEN>`):
```cmd
curl -s -i http://127.0.0.1:8000/api/teams -H "Authorization: Bearer <TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 200 OK`, JSON teams list.

- [ ] **Step 6: Test admin RBAC — admin allowed, regular user denied**

Login as `user@gmail.com` / `myPassword` for a user token, then:
```cmd
curl -s -i http://127.0.0.1:8000/api/admin/teams -H "Authorization: Bearer <USER_TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 403 Forbidden`.

With the admin token:
```cmd
curl -s -i http://127.0.0.1:8000/api/admin/teams -H "Authorization: Bearer <ADMIN_TOKEN>" -H "Accept: application/json"
```
Expected: `HTTP/1.1 200 OK`.

- [ ] **Step 7: Stop the server**

`Ctrl+C`.

- [ ] **Step 8: Commit any fixes**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "fix: resolve smoke-test issues"
```
(Skip if clean.)

---

## Task 16: Run the test suite and clean up Breeze-era stubs

**Files:**
- Possibly delete: `tests\Feature\Auth\*`, `tests\Feature\ProfileTest.php`

- [ ] **Step 1: Run the tests**

Run:
```cmd
cd /d "E:\vs\iitc-api-beta" && php artisan test
```
Expected: tests pass. If they fail on `tests/Feature/Auth/*` or `ProfileTest.php` (Breeze leftovers that reference Blade views we did not port), delete them:
```cmd
rmdir /S /Q "E:\vs\iitc-api-beta\tests\Feature\Auth"
del "E:\vs\iitc-api-beta\tests\Feature\ProfileTest.php"
```
Then re-run `php artisan test`.

- [ ] **Step 2: Commit any test cleanup**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit -m "test: drop Breeze-era stub tests"
```
(Skip if tests passed as-is.)

---

## Task 17: Final verification against acceptance criteria

This is the done-gate. Walk through every checkbox from the spec section 10.

**Files:** none.

- [ ] **Step 1: Verify environment**

```cmd
php -v
```
Expected: `PHP 8.5.3`.

```cmd
cd /d "E:\vs\iitc-api-beta" && composer show laravel/framework | findstr "^versions"
```
Expected: `versions : * 13.x.x`.

- [ ] **Step 2: Verify doctrine/dbal is gone**

```cmd
cd /d "E:\vs\iitc-api-beta" && composer show doctrine/dbal 2>nul && echo "STILL PRESENT - FAIL" || echo "REMOVED OK"
```
Expected: `REMOVED OK`.

- [ ] **Step 3: Verify the Spatie version**

```cmd
cd /d "E:\vs\iitc-api-beta" && composer show spatie/laravel-permission | findstr "^versions"
```
Expected: `versions : * 8.x.x`.

- [ ] **Step 4: Walk the acceptance checklist (spec section 10)**

- [ ] `composer install` completes with no errors on PHP 8.5 — Task 3
- [ ] `php artisan key:generate`, `migrate:fresh --seed` succeed — Task 14
- [ ] `php artisan serve` boots; `GET /api` returns `'ok! @iitc'` — Task 15 Step 2
- [ ] `GET /api/competitions` returns 200 — Task 15 Step 3
- [ ] `POST /api/register` works — Task 15 Step 4 (login flow); optionally test directly
- [ ] Admin routes reject non-admin (403) and accept admin (200) — Task 15 Step 6
- [ ] `php artisan test` passes — Task 16
- [ ] PHP 8.5.3, Laravel 13 — Step 1-2 above
- [ ] No `doctrine/dbal` in lock — Step 2 above
- [ ] Super Admin gate works — login as `superadmin@gmail.com` / `myPassword`; verify any authorized route returns 200

- [ ] **Step 5: Clean up the temp skeleton**

Run:
```cmd
rmdir /S /Q "E:\vs\iitc-api-l13-skeleton"
```
Expected: temp directory removed.

- [ ] **Step 6: Final commit + summary log**

```cmd
git -C "E:\vs\iitc-api-beta" add -A
git -C "E:\vs\iitc-api-beta" commit --allow-empty -m "chore: complete Laravel 13 migration verification"
git -C "E:\vs\iitc-api-beta" log --oneline chore/upgrade-laravel-13 ^main
```
Expected: a clean log of the migration commits on the branch.

---

## Rollback (if everything went wrong)

```cmd
git -C "E:\vs\iitc-api-beta" checkout main
git -C "E:\vs\iitc-api-beta" branch -D chore/upgrade-laravel-13
```
The `pre-laravel-13-migration` tag on `main` preserves the exact pre-migration state.
