## graphify

This project has a knowledge graph at graphify-out/ with god nodes, community structure, and cross-file relationships.

Rules:
- For codebase questions, first run `graphify query "<question>"` when graphify-out/graph.json exists. Use `graphify path "<A>" "<B>"` for relationships and `graphify explain "<concept>"` for focused concepts. These return a scoped subgraph, usually much smaller than GRAPH_REPORT.md or raw grep output.
- If graphify-out/wiki/index.md exists, use it for broad navigation instead of raw source browsing.
- Read graphify-out/GRAPH_REPORT.md only for broad architecture review or when query/path/explain do not surface enough context.
- After modifying code, run `graphify update .` to keep the graph current (AST-only, no API cost).

# Seminar Certificate Feature

## Data Model
- `seminar_registrations` table: `certificate_number` (string, unique), `certificate_path` (file path)
- `settings` table: key-value store. Keys: `non_winner_label` (default "Partisipasi")
- `Setting` model: `get(key, default)`, `set(key, value)` static helpers

## Certificate Logic (GET /api/seminar)
- `SeminarController@index` enriches each registration: `teamId`, `teamName`, `competitionName`, `winnerStatus`, `certificateSeminar`
- Team lookup: `TeamController::findUserTeam($user)` — finds user's team in active event
- `certificateSeminar`: uploaded file URL (`certificate_path`) when file exists, otherwise `null`
- `winnerStatus`: winner → `"{award_title} (Rank {rank})"`, non-winner with team → non-winner label, no team → `null`

## New Files
- `app/Models/Setting.php` — key-value settings
- `app/Http/Controllers/Admin/AdminSettingController.php` — `updateNonWinnerLabel` (PUT /api/admin/settings/non-winner-label)
- `resources/views/admin/seminar/certificates.blade.php` — admin certificate management UI

## New API Routes (auth:sanctum, /admin prefix)
- `POST /admin/seminar/{userId}/upload-certificate` — upload cert file for winners
- `PUT /admin/settings/non-winner-label` — update non-winner label
- `POST /admin/teams/{teamId}/avatar` — upload team avatar
- `PUT /admin/teams/{teamId}/name` — edit team name

## New Web Routes (auth, admin, super-admin)
- `GET /admin/seminar/certificates` — certificate management page
- `PUT /admin/seminar/certificates/update-label` — save non-winner label
- `POST /admin/seminar/certificates/upload` — upload cert file (web form), auto-generate certificate number
- `POST /admin/teams-management/{team}/avatar` — upload avatar (web form)

## Admin Views
- `admin/seminar/certificates` — table with stats, non-winner label setting, search/filter, per-row: Upload Sertifikat (all), Lihat if certificate exists
- `admin/teams-management` — Avatar button per row, modal upload form, avatar thumbnail in team name column

## Controller Methods Added
- `SeminarAdminController`: `certificates()`, `updateCertificateLabel()`, `uploadCertificateWeb()`
- `AdminTeamManagementController`: `uploadAvatar()`
- `Admin\TeamController`: `uploadAvatar()`, `updateName()`
- `SeminarController`: enriched `index()` response
- `AdminSettingController`: `updateNonWinnerLabel()`