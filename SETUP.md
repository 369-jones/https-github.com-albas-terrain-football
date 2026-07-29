# Terrain Football — setup & go-live checklist

## Local development

```bash
composer install
npm install
cp .env.example .env   # then fill in DB_*, PAYSTACK_*, FLUTTERWAVE_* (test keys are fine locally)
php artisan key:generate
php artisan migrate
php artisan storage:link
composer run dev        # server + queue:listen + pail + vite, all together
```

Demo login: `admin@universite.ci` / `Admin@2024` (has both the `owner` and `finance` roles).

## Environment variables that matter beyond the basics

| Variable | Why |
|---|---|
| `PAYSTACK_SECRET_KEY`, `FLUTTERWAVE_SECRET_KEY`, `FLUTTERWAVE_WEBHOOK_HASH` | Payment verification and webhook signature checks fail closed without these — bookings simply never confirm. |
| `SESSION_SECURE_COOKIE` | Must be `true` in production (HTTPS-only cookies). Left `false` for local HTTP dev. |
| `BACKUP_NOTIFICATION_EMAIL` | Failed/unhealthy backups are silent without this. |
| `BACKUP_ARCHIVE_PASSWORD` | Encrypts backup zips at rest. Optional but recommended. |
| `DB_DUMP_BINARY_PATH` | Only needed locally if `mysqldump` isn't on PATH (some Windows/XAMPP installs) — leave unset in production. |

## Go-live checklist

### Already handled in code (nothing to do)
- [x] Webhook signature verification (Paystack HMAC, Flutterwave `verif-hash`) — tested, rejects invalid/missing signatures with 401
- [x] Booking/payment endpoints throttled (`throttle:10,1`); login throttled (`throttle:5,1` — added manually since this app uses a custom `AuthController`, not Fortify, so no rate limiting shipped for free)
- [x] `URL::forceScheme('https')` + security headers (CSP, HSTS, X-Frame-Options, etc.) auto-enable when `APP_ENV=production`
- [x] `composer.lock` / `package-lock.json` present and current — `composer audit` and `npm audit` both clean as of this pass (15 CVEs in transitive deps — dompdf, guzzle, psr7 — were found and patched)
- [x] Automated backups configured (`spatie/laravel-backup`) — daily DB + files backup, cleanup, and health monitoring wired into the scheduler (`routes/console.php`). Verified the file-backup path end-to-end locally; the DB-dump path is config-correct but couldn't be fully exercised on this dev machine (see note below).
- [x] Double-booking race condition closed at both the app layer (`Booking::overlaps()` inside a locked transaction) and the DB layer (unique constraint on `pitch_id`+`booking_date`+`start_time`) — both have test coverage
- [x] Payout double-claim race condition closed the same way (`lockForUpdate()` before creating `payout_items`)

### Do this before pointing a real domain at it
- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true` in the production `.env`
- [ ] Put the site behind Cloudflare (or similar) for DDoS protection and a WAF — infrastructure, not something I can configure from here
- [ ] Point the server's crontab at the scheduler: `* * * * * php artisan schedule:run >> /dev/null 2>&1` — without this, backups, the daily `bookings:complete-past` job, and backup cleanup never run
- [ ] Run a **queue worker under a process supervisor** (e.g. `supervisord`), not just `php artisan queue:work` in a terminal — booking-confirmation emails are queued (`ShouldQueue`) and will sit in the `jobs` table forever without one. Verified locally that dispatch → queue → worker → rendered email all work; what's missing is *supervision* so the worker survives a crash/reboot.
- [ ] Restrict SSH to key-only auth, enable a firewall (ufw), enable automatic OS security updates
- [ ] File permissions: only `storage/` and `bootstrap/cache/` should be writable by the web server user — never the whole project
- [ ] Add the `s3` disk to `config/backup.php`'s `destination.disks` once `AWS_*` credentials exist — local-only backups don't survive losing the server itself
- [ ] Swap in real `PAYSTACK_*`/`FLUTTERWAVE_*` **live** keys and register the real webhook URLs in each provider's dashboard
- [ ] This project has no git repository yet — go-live implies a deploy pipeline, which implies version control. Worth setting up before anything else here, if it isn't already handled elsewhere.

### Known local-machine-only limitation (not a production concern)
Live-testing `php artisan backup:run` against the DB on this Windows/XAMPP dev box hit two purely local issues: `mysqldump` wasn't on PATH (fixed via `DB_DUMP_BINARY_PATH`), and this XAMPP's bundled `mysqldump` is too old to speak the `caching_sha2_password` auth plugin the local MySQL server uses. Neither applies to a real Linux deployment, where `mysqldump` ships matched to the server version and is already on PATH — but it does mean the DB-backup path specifically wasn't verified with a live run here, only reviewed. Worth running `php artisan backup:run --only-db` once by hand on the actual production server after deploy to confirm.

## What's next (future phases, not required to go live)
- Real Paystack/Flutterwave *payout* API integration — `PayoutController::markPaid` is currently a manual finance-role confirmation, not an actual bank transfer
- SMS notifications (no provider configured — see the comment in `BookingConfirmedNotification`)
- Pitch owner forms for reviews moderation, analytics (occupancy trends, popular time slots)
