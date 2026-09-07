# AGENTS.md

Rig meeting reminder + WhatsApp attendance system, PT Besmindo (Operasional Rig). Web app + local WhatsApp gateway.

## CRITICAL: two parallel CodeIgniter stacks coexist

- **CI3 (`application/`, root `index.php`, root `.htaccess`) = the LIVE app.** Hitting `http://localhost/Project%20Besmindo%20Reminder/...` boots CI3 (verified live: login posts to `auth/attempt_login`). Edit CI3 code here for behavior changes.
- **CI4 (`app/` + `public/index.php` + `.env` + `spark`) = in-progress migration / mirror** of the same app, same DB, same features. NOT what the baseURL serves (needs docroot→`public/` or `php spark serve`). README.md and `.env` describe the CI4 layout, not what runs under plain XAMPP.
- Do not assume changes apply to both. CI3 and CI4 are logically duplicated; e.g. CI3 login route `auth/attempt_login` vs CI4 `auth/attemptLogin` differ. Mirror a fix in the other stack only if the task intends both.

## Run

- Web: XAMPP Apache + MySQL, then open `http://localhost/Project%20Besmindo%20Reminder/`. Demo login `admin` / `admin123`.
- First-time DB: visit `/install` (auto-creates DB `db_besmindo_reminder`, 8 tables, demo rigs/crews/meetings). Schema source: `database.sql`.
- WhatsApp gateway (REQUIRED for sending): run `JALANKAN-GATEWAY.bat` (keeps Node `whatsapp-gateway/server.js` alive, port 3000). Scan QR at `/reminder/gateway`. Without it all send actions fail.

## Data flow

Single MySQL DB `db_besmindo_reminder`, tables: `users rigs crews meetings meeting_participants reminders_log attendances app_settings`.

- Reminder tiers (H-1, 1-hr, 15-min) are computed in `application/controllers/Api.php::check_reminders()` (`get('/api/check-reminders')`). There is NO real OS cron; this endpoint is polled by the browser "Scan Reminders" button / topbar. It inserts into `reminders_log` with `status='sent'` directly (logs without real dispatch).
- Actual WhatsApp send path: `Reminder::send/broadcast` → `Whatsapp_service` library → gateway queue at `app_settings.wa_api_url` (default `http://localhost:3000/send-message` / `/send-bulk`).
- Attendance: `Api::sync_teams_live` (`/api/sync-teams-live`, CORS-open POST) ingests a roster of attendee names (Teams robot) and matches crew by name (exact → substring → word tokens → `similar_text` ≥75%). Late if after meeting_start + 10 min. Statuses: HADIR, TERLAMBAT, BELUM_HADIR, TIDAK_HADIR, IZIN/SAKIT.
- View pages render via `MY_Controller::render_template` (header/sidebar/navbar + view + footer). All non-auth controllers extend `MY_Controller` which enforces `logged_in` session.

## CI3 code layout

- `application/config/routes.php` — default_controller `dashboard`, alias routes.
- `application/controllers/` — Auth, Dashboard, Crew, Rig, Meeting, Reminder, Attendance, Report, Api, Install (Install only pre-login).
- `application/models/*_model.php` — CI3 style; `Setting_model` stores key/value in `app_settings` (e.g. `wa_api_url`, `wa_gateway_provider`).
- `application/libraries/Whatsapp_service.php` — normalize phone (0→62 prefix, group `@g.us` passthrough), send/bulk via gateway.
- `application/views/` — login, dashboard, and per-module dirs. Templates under `application/views/templates/`.

## WhatsApp gateway (Node) notes

- `whatsapp-gateway/server.js` (Baileys/Express). Endpoints: `/status`, `/queue-status`, `/groups`, `/group-participants`, POST `/send-message`, `/send-bulk`, `/logout`.
- Anti-ban: personal messages ~15–35 s apart, max 25 personal/hr, simulated typing. Group sends skip the per-hour cap and use shorter delay. Broadcasts are queued; `send-bulk` returns `{queued, queueLength, estimatedMinutes}`.
- `wa_group_id` per rig (from `rigs` table, `@g.us`) enables group broadcast.

## Gotchas

- Root has BOTH `assets/` and `public/assets/` (and `public/index.php`) — separate dups of static files; CI3 uses root `assets/`.
- `.env` (CI4) is gitignored; live DB/URL config for CI4 lives there. CI3 DB creds are in `application/config/database.php`. Both point to `db_besmindo_reminder` (root/no password on XAMPP).
- Reminders "H-1_JAM" tier key uses underscore suffix in logs (`H-1_JAM`), while labels are "H-1", "1 Jam", "15 Menit".
- Timezone WIB (`Asia/Jakarta`); date comparisons use PHP `date()` server-local time.
- CI3 views are Indonesian; keep strings/lang consistent.
