# Deployment

The project is prepared for a short public demo on Railway or any Docker-based host.

## Recommended Railway Setup

1. Create a Railway project from this repository.
2. Railway will detect `Dockerfile` and `railway.json`.
3. Add a volume and mount it to `/data`.
4. Set production variables:

```env
APP_NAME=Journal
APP_ENV=production
APP_KEY=base64:paste-generated-key-here
APP_DEBUG=false
APP_URL=https://your-railway-domain

DB_CONNECTION=sqlite
DB_DATABASE=/data/database.sqlite

LOG_CHANNEL=stderr
LOG_LEVEL=warning

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

RUN_DEMO_SEEDER=true
DEMO_ADMIN_EMAIL=admin@journal.demo
DEMO_ADMIN_PASSWORD=replace-with-strong-password
DEMO_TEACHER_EMAIL=teacher@journal.demo
DEMO_TEACHER_PASSWORD=replace-with-strong-password
DEMO_STUDENT_EMAIL=student@journal.demo
DEMO_STUDENT_PASSWORD=replace-with-strong-password
```

Generate an app key locally:

```bash
php artisan key:generate --show
```

## Startup

The container runs `deploy/start.sh`.

On every start it:

- creates the SQLite file if it does not exist;
- runs migrations;
- runs `DemoSeeder` when `RUN_DEMO_SEEDER=true`;
- caches config and Blade views;
- starts Laravel on the host-provided `$PORT`.

## Current Local Data

The current local `database/database.sqlite` is intentionally excluded from Docker builds. Upload it only after confirming that it is safe to publish the contained students, users, and attendance records.
