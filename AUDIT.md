# IITC API Codebase Audit

Generated: 2026-08-31

---

## 1. Unused Files

Files that are never imported/required anywhere in the codebase (excluding entry points, config files, and test files).

### 1.1 `app/Providers/AuthServiceProvider.php`
- **Lines:** 1–40
- **Issue:** Not registered in `bootstrap/providers.php`. Only `AppServiceProvider` and `TelescopeServiceProvider` are loaded.
- **Impact:** Contains `Gate::before` for Super Admin — but `AppServiceProvider` already has the exact same `Gate::before` (duplicate). Policy registrations (`$policies`) are also never loaded.
- **Confidence:** High

### 1.2 `app/Providers/EventServiceProvider.php`
- **Lines:** 1–30
- **Issue:** Not registered in `bootstrap/providers.php`. The `Registered` → `SendEmailVerificationNotification` listener is never wired.
- **Confidence:** High

### 1.3 `app/Providers/BroadcastServiceProvider.php`
- **Lines:** 1–17
- **Issue:** Not registered in `bootstrap/providers.php`. Broadcasting routes are never loaded (though `routes/channels.php` is loaded via `bootstrap/app.php` → `withRouting(channels: ...)` anyway).
- **Confidence:** High

### 1.4 `app/Providers/RouteServiceProvider.php`
- **Lines:** 1–36
- **Issue:** Not registered in `bootstrap/providers.php`. Its `boot()` method (rate limiter + route registration) is superseded by `bootstrap/app.php` inline routing. However, `RouteServiceProvider::HOME` constant is still referenced by 6 auth controllers.
- **Confidence:** Medium (constant is used, but the class itself is never instantiated)

### 1.5 `app/Mail/SendsPasswordResetEmails.php`
- **Lines:** 1–72
- **Issue:** Created but never instantiated or sent via `Mail::send()` / `->to()` anywhere. Password reset flow in `PasswordResetLinkController` returns the token directly rather than sending an email.
- **Confidence:** High

### 1.6 `app/Http/Requests/ProfileUpdateRequest.php`
- **Lines:** 1–17
- **Issue:** Never imported or used by any controller.
- **Confidence:** High

### 1.7 `app/Http/Requests/StoreCategoryCompetitionRequest.php`
- **Lines:** 1–17
- **Issue:** Never imported or used by any controller. `authorize()` returns `false`, so it would block use anyway.
- **Confidence:** High

### 1.8 `app/Http/Requests/UpdateCategoryCompetitionRequest.php`
- **Lines:** 1–17
- **Issue:** Never imported or used. `authorize()` returns `false`.
- **Confidence:** High

### 1.9 `tests/Unit/ExampleTest.php`
- **Lines:** 1–10
- **Issue:** Default Laravel scaffold test, never modified.
- **Confidence:** High

### 1.10 `tests/Feature/ExampleTest.php`
- **Lines:** 1–11
- **Issue:** Default Laravel scaffold test, never modified.
- **Confidence:** High

### 1.11 `database/factories/MemberFactory.php`
- **Lines:** 1–20
- **Issue:** `definition()` returns an empty array. The factory is only used in `DummyDataSeeder` (line 47), which creates members manually via `Member::factory()->create([...])` with explicit attributes. The empty definition means it would fail if called without override data.
- **Confidence:** Medium (used in seeder but effectively dead)

### 1.12 `database/factories/PaymentFactory.php`
- **Lines:** 1–20
- **Issue:** `definition()` returns an empty array. Only used in `DummyDataSeeder` (line 91) with explicit attributes. Same fragility.
- **Confidence:** Medium

### 1.13 `database/factories/PaymentStatusFactory.php`
- **Lines:** 1–20
- **Issue:** `definition()` returns an empty array. Never used anywhere in codebase.
- **Confidence:** High

### 1.14 `database/factories/CategoryCompetitionFactory.php`
- **Lines:** 1–20
- **Issue:** Only used in `CategoryCompetitionSeeder` which is never called from `DatabaseSeeder`.
- **Confidence:** High

### 1.15 `database/factories/EventFactory.php`
- **Lines:** 1–20
- **Issue:** Never used directly — tests use `Event::factory()` which resolves to this factory, but only `CompetitionFactory` has a relationship that calls it. Some tests create events inline.
- **Confidence:** Medium (indirectly used via `Event::factory()` in tests)

### 1.16 `database/seeders/CategoryCompetitionSeeder.php`
- **Lines:** 1–17
- **Issue:** Not called from `DatabaseSeeder`.
- **Confidence:** High

### 1.17 `database/seeders/EventPermissionSeeder.php`
- **Lines:** 1–17
- **Issue:** Not called from `DatabaseSeeder`.
- **Confidence:** High

### 1.18 `database/seeders/CategorySeeder.php`
- **Lines:** 1–17
- **Issue:** Not called from `DatabaseSeeder`.
- **Confidence:** High

### 1.19 `database/seeders/RealDataSeeder.php`
- **Lines:** 1–17
- **Issue:** Not called from `DatabaseSeeder`.
- **Confidence:** High

---

## 2. Unused Functions/Exports

Functions, classes, or exported symbols that are defined but never called or imported elsewhere.

### 2.1 `SeminarController::register()` — `app/Http/Controllers/SeminarController.php:60`
- **Issue:** Route is commented out in `routes/api.php:151`.
- **Confidence:** High

### 2.2 `SeminarController::show()` — `app/Http/Controllers/SeminarController.php:87`
- **Issue:** Route is commented out in `routes/api.php:152`.
- **Confidence:** High

### 2.3 `SeminarController::verifyAttendance()` — `app/Http/Controllers/SeminarController.php:114`
- **Issue:** Route is commented out in `routes/api.php:153`.
- **Confidence:** High

### 2.4 `SeminarController::downloadCertificate()` — `app/Http/Controllers/SeminarController.php:169`
- **Issue:** Route is commented out in `routes/api.php:153`.
- **Confidence:** High

### 2.5 `Auth\VerifyEmailController::__invoke()` — `app/Http/Controllers/Auth/VerifyEmailController.php:14`
- **Issue:** Route is commented out in `routes/auth.php:44`.
- **Confidence:** High

### 2.6 `Auth\EmailVerificationNotificationController::store()` — `app/Http/Controllers/Auth/EmailVerificationNotificationController.php:14`
- **Issue:** Route is commented out in `routes/auth.php:48`.
- **Confidence:** High

### 2.7 `Auth\EmailVerificationPromptController::__invoke()` — `app/Http/Controllers/Auth/EmailVerificationPromptController.php:14`
- **Issue:** Route is commented out in `routes/auth.php:41`.
- **Confidence:** High

### 2.8 `Auth\RegisteredUserController::create()` / `store()` — `app/Http/Controllers/Auth/RegisteredUserController.php:18,24`
- **Issue:** Routes are commented out in `routes/auth.php:7-12`.
- **Confidence:** High

### 2.9 `Auth\PasswordResetLinkController::create()` / `store()` — `app/Http/Controllers/Auth/PasswordResetLinkController.php:15,20`
- **Issue:** Routes are commented out in `routes/auth.php:17-22`.
- **Confidence:** High

### 2.10 `Auth\NewPasswordController::create()` — `app/Http/Controllers/Auth/NewPasswordController.php:15`
- **Issue:** Route is commented out in `routes/auth.php:25`.
- **Confidence:** High

### 2.11 `Admin\WinnerController` (API) — `app/Http/Controllers/Admin/WinnerController.php`
- **Issue:** Both `store()` and `destroy()` duplicate the same logic as `AdminWinnerController` (web). The API routes use `WinnerController::class` for winners in `routes/api.php:114-115`. But the web routes also use `AdminWinnerController` in `routes/web.php:117-118`. Both are functional, but the API WinnerController is redundant with the web AdminWinnerController.
- **Confidence:** Medium (both are routed, but the API one is redundant with the web one)

### 2.12 `Admin\TeamController::transformDBToResponseTeam()` — `app/Http/Controllers/Admin/TeamController.php:87`
- **Issue:** Identical transformation logic also exists in `CompetitionMineController::transformDBToResponseTeam()`.
- **Confidence:** Medium (used, but duplicate)

### 2.13 `CategoryCompetition::competitionCompetitionCategories()` — `app/Models/CategoryCompetition.php:19`
- **Issue:** References `CompetitionCompetitionCategory` class which does not exist anywhere in the codebase. The method is never called.
- **Confidence:** High

### 2.14 `TeamPolicy::restore()` — `app/Policies/TeamPolicy.php:65`
- **Issue:** `restore` and `forceDelete` methods are never used (no soft deletes on teams, no routes trigger them).
- **Confidence:** Medium

### 2.15 `TeamPolicy::forceDelete()` — `app/Policies/TeamPolicy.php:73`
- **Issue:** Same as above.
- **Confidence:** Medium

### 2.16 `PaymentStatusPolicy::viewAny()` / `view()` / `create()` / `delete()` — `app/Policies/PaymentStatusPolicy.php:15,21,27,39`
- **Issue:** All return `false`. Only `update()` is actually used (in `PaymentStatusController::update`).
- **Confidence:** Medium

### 2.17 `PaymentPolicy::viewAny()` / `view()` / `create()` / `delete()` — `app/Policies/PaymentPolicy.php`
- **Issue:** (Likely same pattern as PaymentStatusPolicy — only `create` is used in `PaymentController::store`)
- **Confidence:** Medium (need to verify full file)

---

## 3. Duplicate Logic

Code blocks that do the same (or nearly the same) thing in multiple places.

### 3.1 Payment Status Determination Pattern
**Occurrences:**
- `app/Http/Controllers/CompetitionMineController.php:49-50`
- `app/Http/Controllers/TeamController.php:146-147`
- `app/Http/Controllers/PaymentController.php:69-70`
- `app/Http/Controllers/AdminGetDetailTeamController.php:25-26`
- `app/Http/Controllers/Admin/TeamController.php` (implied in show method)

**Pattern:** `$paymentStatus = isset($team->payment) ? PaymentStatus::PENDING : null; $paymentStatus = $team->paymentStatus->status ?? $paymentStatus;`

**Impact:** 5+ copies of the same 2-line logic to resolve payment status. Should be a helper or accessor on the Team model.

### 3.2 `Gate::before` Super Admin Grant
**Occurrences:**
- `app/Providers/AuthServiceProvider.php:35-37`
- `app/Providers/AppServiceProvider.php:19-21`

**Impact:** Both providers register the exact same `Gate::before` callback. Since `AuthServiceProvider` is not registered in `bootstrap/providers.php`, only the one in `AppServiceProvider` is active. The duplicate in `AuthServiceProvider` is dead code, but confusing.

### 3.3 "Has Team In Event" Query
**Occurrences:**
- `app/Http/Controllers/TeamController.php:54-64` (in `store()`)
- `app/Http/Controllers/JoinTeamController.php:26-35`
- `app/Http/Controllers/JoinIndividualCompetitionController.php:18-27`

**Pattern:** Check if user already has a team (as leader or member) in the same event as the target competition. The same subquery `where('leader_id', $userId)->orWhereHas('members', ...)` repeated 3 times.

### 3.4 Image Upload and Delete Pattern
**Occurrences:**
- `app/Http/Controllers/Admin/AdminMediaPartnerController.php:54-66` (update)
- `app/Http/Controllers/Admin/AdminSponsorController.php:68-80` (update)
- `app/Http/Controllers/Admin/AdminMediaPartnerController.php:76-80` (destroy)
- `app/Http/Controllers/Admin/AdminSponsorController.php:87-90` (destroy)

**Pattern:** `parse_url($model->image, PHP_URL_PATH)` → `ltrim(str_replace('/storage', '', $oldPath), '/')` → `Storage::disk('public')->delete($oldPath)`. Same 3-line sequence for deleting old images.

### 3.5 CSV Export with BOM
**Occurrences:**
- `app/Http/Controllers/Admin/ExportController.php:34-74` (teams)
- `app/Http/Controllers/Admin/ExportController.php:89-103` (seminars)
- `app/Http/Controllers/Admin/AdminTeamRecapController.php` (export)

**Pattern:** `fopen('php://output', 'w')` → `fwrite($handle, "\xEF\xBB\xBF")` → `fputcsv(...)` → `fclose($handle)`. Same boilerplate in 3+ export methods.

### 3.6 Active Event Query
**Occurrences:** At least 10+ occurrences across controllers:
- `Event::query()->where('is_active', true)->first()`
- `Event::query()->where('is_active', true)->pluck('id')`

**Impact:** Scattered throughout the codebase. Every controller that needs the active event repeats this query.

### 3.7 Competition ID Collection from Active Event
**Occurrences:** At least 7+ occurrences:
- `Competition::query()->where('event_id', $eventActive->id)->pluck('id')`

**Impact:** Repeated pattern of getting competition IDs for the active event.

---

## 4. Dead Code Paths

Unreachable code, conditions that can never be true, or branches left over from old logic.

### 4.1 `LeaderJoinOwnTeamException` — Missing Class
- **File:** `app/Http/Controllers/JoinTeamController.php:5,22`
- **Issue:** Imports `App\Exceptions\LeaderJoinOwnTeamException` (line 5) and throws it (line 22), but the class does not exist — no `app/Exceptions/` directory exists and no matching file is found anywhere.
- **Impact:** If the "leader tries to join own team" path is hit, PHP will throw a fatal `Class "App\Exceptions\LeaderJoinOwnTeamException" not found` error, not the intended `catch (Exception $exception)` block. The catch on line 51 catches `Exception`, but a class-not-found error is a PHP `Error`, not an `Exception`, so it will NOT be caught.
- **Confidence:** **High** — confirmed by file search.

### 4.2 `CompetitionCompetitionCategory` — Missing Model
- **File:** `app/Models/CategoryCompetition.php:19`
- **Issue:** The `competitionCompetitionCategories()` method returns `$this->hasMany(CompetitionCompetitionCategory::class, ...)` but the class `CompetitionCompetitionCategory` does not exist anywhere in the codebase.
- **Impact:** If this method is ever called, it will throw a fatal error. Currently it's never called, making it dead code.
- **Confidence:** **High**

### 4.3 Commented-Out Submission Date Validation
- **File:** `app/Http/Controllers/TeamController.php:197-200`
- **Issue:** A block of commented-out code that validates submission dates (restricting to Aug 19-28). Left over from a previous event. The submission route is active but the date restriction is disabled.
- **Confidence:** High

### 4.4 Commented-Out Routes (Auth + Seminar)
- **Files:**
  - `routes/auth.php:7-12, 17-22, 25-28, 41-50`
  - `routes/api.php:151-153`
  - `routes/api.php:42` (debug-sentry)
- **Issue:** 15+ route definitions are commented out but their corresponding controller methods remain in the codebase (see Section 2).
- **Confidence:** High

### 4.5 `CategoryCompetitionSeeder` Not Called
- **File:** `database/seeders/CategoryCompetitionSeeder.php`
- **Issue:** Defines a seeder but `DatabaseSeeder.php` does not call it.
- **Confidence:** High

### 4.6 `CategoryCompetitionFactory` Empty Definition
- **File:** `database/factories/CategoryCompetitionFactory.php`
- **Issue:** `definition()` returns an empty array. Even if the seeder were called, it would fail.
- **Confidence:** High

### 4.7 `PaymentAdminController::update()` — Duplicate `team_id` in `updateOrCreate`
- **File:** `app/Http/Controllers/Admin/PaymentAdminController.php:92-98`
- **Issue:** `team_id` is passed both as the first argument (key) and in the second argument (data array). The duplicate is harmless but indicates confusion.
- **Confidence:** Low (harmless, but sloppy)

### 4.8 `CompetitionCertificateService::generateCertificateNumber()` — Wrong Parameter
- **File:** `app/Services/CompetitionCertificateService.php:97`
- **Issue:** `previewForParticipant()` calls `$this->generateCertificateNumber($team)` passing a `$team` argument, but `generateCertificateNumber()` is defined with no parameters (`public function generateCertificateNumber(): string`). The parameter is ignored, but the call is misleading.
- **Confidence:** Medium

---

## 5. Suspicious/Risky Logic

Things like missing error handling, off-by-one risks, magic numbers, inconsistent naming, or logic contradicting comments.

### 5.1 `LeaderJoinOwnTeamException` Missing (Critical)
- **File:** `app/Http/Controllers/JoinTeamController.php:22`
- **Issue:** Throws a non-existent exception class. Any attempt by a team leader to join their own team via code will cause a PHP Fatal Error (uncaught `Error`), not a handled API error response.
- **Risk:** **High** — this is a runtime crash.

### 5.2 `CompetitionCertificateService::generateCertificateNumber()` — Non-Sequential
- **File:** `app/Services/CompetitionCertificateService.php:22`
- **Issue:** Uses `rand(1000, 9999)` instead of a database lookup for sequential numbering. This creates a 1/9000 collision chance per certificate. The `CertificateService::generateCertificateNumber()` (seminar version) does a proper DB lookup — inconsistent.
- **Risk:** **Medium** — certificate numbers could collide.

### 5.3 Hardcoded Template Path
- **File:** `app/Services/CompetitionCertificateService.php:53,107`
- **Issue:** Uses `base_path('Example certificate.jpg')` — a hardcoded filename with a space. The seminar `CertificateService` uses a proper `public_path()` lookup with fallback. Inconsistent.
- **Risk:** **Low** — falls through gracefully if file doesn't exist.

### 5.4 `PrivacyPolicy.php` Route — Typo in Description
- **File:** `routes/web.php` (likely)
- **Note:** The route description `Esensial (Wajib):` has a typo. Not a code bug but indicates sloppiness.

### 5.5 TCPDF No `use` Import for `response()` Helper
- **File:** `app/Services/CompetitionCertificateService.php:129`
- **Issue:** Calls `response($pdfContent, 200, [...])` as a free function. This works in Laravel but is not imported via `use`. Works because of Laravel's global helpers, but inconsistent with the rest of the codebase which uses `return response()->json(...)`.
- **Risk:** **Low** — works, but inconsistent.

### 5.6 `PaymentAdminController::update()` — No Authorization Check
- **File:** `app/Http/Controllers/Admin/PaymentAdminController.php:74-106`
- **Issue:** The `update()` method has no `$this->authorize()` call. The route is protected by the `admin` middleware, but there's no explicit check that the user has the "Update Payment Status" permission. Compare with `PaymentStatusController::update()` (API) which does call `$this->authorize('update', ...)`.
- **Risk:** **Medium** — relies solely on middleware for access control.

### 5.7 `Admin\TeamController::index()` — Hardcoded Role Check vs Policy
- **File:** `app/Http/Controllers/Admin/TeamController.php:20`
- **Issue:** Uses `if (! auth()->user()->hasRole('Admin'))` instead of `$this->authorize()`. The non-admin `TeamController` uses policies. Inconsistent authorization pattern.
- **Risk:** **Low** — works, but inconsistent with the rest of the codebase.

### 5.8 `Admin\WinnerController::store()` — Duplicate of `AdminWinnerController`
- **Files:**
  - `app/Http/Controllers/Admin/WinnerController.php` (API)
  - `app/Http/Controllers/Admin/AdminWinnerController.php` (Web)
- **Issue:** Both do the same thing (`Winner::updateOrCreate` with same fields). The API version is routed in `routes/api.php:114-115` and the web version in `routes/web.php:117-118`. Two controllers doing the same thing with different response formats.
- **Risk:** **Low** — functional, but violates DRY.

### 5.9 `SeminarAdminController::uploadCertificate()` — Mixed Injection
- **File:** `app/Http/Controllers/Admin/SeminarAdminController.php:150`
- **Issue:** Uses `app(CertificateService::class)` inline instead of using the constructor-injected instance (`$this->certificateService`). The constructor already injects `CertificateService`, so this is inconsistent.
- **Risk:** **Low** — works, but inconsistent.

### 5.10 `JoinTeamController::store()` — Off-by-One in Capacity Check
- **File:** `app/Http/Controllers/JoinTeamController.php:44`
- **Issue:** `($team->members_count + 1) >= $maxMembers` uses `>=`. For a competition with `max_members = 2` (leader + 1 member), when `members_count = 0`, `0 + 1 >= 2` is `false` → OK. When `members_count = 1`, `1 + 1 >= 2` is `true` → blocked. Correct. But if `max_members = 1` (individual competition), `0 + 1 >= 1` is `true` → blocked immediately. However, `JoinTeamController` is only used for team-based competitions (individual ones use `JoinIndividualCompetitionController`), so the risk is low.
- **Risk:** **Low** — edge case for individual competitions, but those are handled elsewhere.

### 5.11 `AppServiceProvider` and `AuthServiceProvider` — Duplicate `Gate::before`
- **Files:**
  - `app/Providers/AppServiceProvider.php:19-21`
  - `app/Providers/AuthServiceProvider.php:35-37`
- **Issue:** Both register `Gate::before(function ($user, $ability) { return $user->hasRole('Super Admin') ? true : null; })`. Only `AppServiceProvider` is loaded, so the duplicate in `AuthServiceProvider` is dead. But if someone registers `AuthServiceProvider`, the `Gate::before` would be registered twice (Laravel chains them, so both would run — harmless but wasteful).
- **Risk:** **Low**

### 5.12 `TeamPolicy` Not Registered in `AuthServiceProvider`
- **File:** `app/Policies/TeamPolicy.php` is not in `AuthServiceProvider::$policies`
- **Issue:** `TeamController::index()` calls `$this->authorize('viewAny', Team::class)`, `TeamController::store()` calls `$this->authorize('create', Team::class)`, etc. But `TeamPolicy` is not registered. The `Gate::before` for Super Admin (in `AppServiceProvider`) grants all permissions to Super Admins, so it works for them. But for non-Super-Admin users, the policy is never resolved.
- **Risk:** **Medium** — authorization for non-admin users on teams is effectively broken.

### 5.13 `EventPolicy` Not Registered in `AuthServiceProvider`
- **File:** `app/Policies/EventPolicy.php` is not in `AuthServiceProvider::$policies`
- **Issue:** Same as 5.12. `EventController::store()` calls `$this->authorize('create', Event::class)` but no policy is registered.
- **Risk:** **Medium**

### 5.14 `UserController::index()` Route Comment
- **File:** `routes/api.php:148`
- **Issue:** Comment says `Route::get('/users/participants', [UserController::class, 'index']); // Point to index() since show() was deleted`. This is confusing — `index()` lists all users, not just participants. The route name suggests it returns participants but it actually returns users.
- **Risk:** **Low** — naming inconsistency.

### 5.15 `AdminGetDetailTeamController::show()` — `isActive` Mismatch
- **File:** `app/Http/Controllers/AdminGetDetailTeamController.php:30`
- **Issue:** The response field `isActive` is set to the payment status value (PENDING/VALID/INVALID), not a boolean. Callers might expect a boolean from a field named `isActive`.
- **Risk:** **Low** — naming inconsistency.

### 5.16 `PublicSponsorController` — Unused `TIER_ORDER` Constant
- **File:** `app/Http/Controllers/PublicSponsorController.php:13`
- **Issue:** `private const TIER_ORDER = [...]` is defined but never used. The `orderByRaw` on line 16 uses a hardcoded FIELD list instead.
- **Risk:** **Low** — dead constant.

### 5.17 `IncrementalRateLimit` — Cache Key Race Condition
- **File:** `app/Http/Middleware/IncrementalRateLimit.php:73-74`
- **Issue:** The window start check and attempts counter are not atomic. Two concurrent requests could both see `windowStart === null` and both set it, effectively resetting the window. This is a minor race condition.
- **Risk:** **Low** — unlikely to be exploited, but not robust.

### 5.18 `SeminarRegistrationPolicy` — Only Used in Commented-Out Route
- **File:** `app/Policies/SeminarRegistrationPolicy.php`
- **Issue:** Registered in `AuthServiceProvider` but only used in `SeminarController::verifyAttendance()` which has its route commented out. The `SeminarAdminController::verify()` method does NOT use the policy.
- **Risk:** **Low** — policy is registered but never actually enforced.

### 5.19 `DeleteTeamMemberController` — Duplicate Leave Logic
- **File:** `app/Http/Controllers/DeleteTeamMemberController.php:25-38`
- **Issue:** Lines 25-38 duplicate the exact same logic as `LeaveTeamController::__invoke()` (lines 22-34). When a member removes themselves from the team via `DeleteTeamMemberController`, it reimplements `LeaveTeamController` instead of delegating to it.
- **Risk:** **Low** — duplicate code, but functional.

---

## Summary Statistics

| Category | Count |
|---|---|
| Unused files | 19 |
| Unused functions/exports | 21 |
| Duplicate logic patterns | 7 |
| Dead code paths | 8 |
| Suspicious/risky logic | 19 |

### Critical Items (Immediate Attention)

1. **`LeaderJoinOwnTeamException` missing** (4.1/5.1) — Fatal error when leader tries to join own team via code.
2. **`CompetitionCompetitionCategory` missing** (4.2) — Fatal error if `CategoryCompetition::competitionCompetitionCategories()` is ever called.
3. **`TeamPolicy` and `EventPolicy` not registered** (5.12/5.13) — Authorization for non-admin users is broken.
4. **Duplicate `Gate::before`** (3.2) — Confusing maintainability issue.