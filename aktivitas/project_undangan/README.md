# Undangan PHP project

## First-time dashboard setup

1. Start Apache and MySQL in Laragon. Make sure the `db_undangan` database and its `acara` and `rsvp` tables exist. If needed, import `db_undangan.sql` in phpMyAdmin.
2. Open Laragon Terminal in this folder and run:

   ```powershell
   php setup-dashboard.php
   ```

   This safely adds the event fields needed by the dashboard and creates/updates the dashboard admin account. It keeps existing event and RSVP rows. The script asks you to choose an admin username and password.
3. Open `http://localhost/project_undangan/dashboard.html`, sign in, and save event details. Guests open `http://localhost/project_undangan/` to see the welcome page, then choose **Buka Undangan** to continue to `undangan.php`. The invitation reads saved data from MySQL.

The dashboard can edit couple names, date, city, venue, address, quote, ceremony and reception times, family details, cover and couple photos, invitation music, and gallery photos. Gallery photos can be added, replaced, reordered, or removed. RSVP submissions are stored in the `rsvp` table and can be reviewed, edited, or deleted from the dashboard.

After updating an existing database with a newer copy of this project, run `php setup-dashboard.php --migrate-only` from Laragon Terminal so any new event fields are added safely.

The app uses the local MySQL settings in `config.php`. Update them if your Laragon MySQL account is different.

## Deploying this PHP app on Vercel

This project uses Vercel's Container runtime with FrankenPHP. The `Dockerfile`, `Caddyfile`, and `vercel.json` make Vercel execute the PHP pages instead of downloading them. Keep the Vercel project's **Root Directory** set to the folder containing these files, then redeploy from GitHub.

The live site cannot connect to Laragon's `127.0.0.1` database on your computer. Create or use a MySQL database with a connection allowed from Vercel. Import `db_undangan.sql` into it, then run `php setup-dashboard.php --migrate-only` from your computer with the `DB_*` variables temporarily set to the remote database. Run `php setup-dashboard.php` once to create the dashboard login. This setup script is intentionally blocked from public web requests. If your current Laragon database already has the right data, export that database from phpMyAdmin and import the export into the cloud database instead; keep a backup before importing.

Add these variables in Vercel Project Settings → Environment Variables. Use the database provider's actual connection details; never commit its password:

| Variable | Value |
| --- | --- |
| `DB_HOST` | Remote MySQL hostname |
| `DB_PORT` | Remote MySQL port, usually `3306` |
| `DB_DATABASE` | Database name, e.g. `db_undangan` |
| `DB_USERNAME` | Remote database username |
| `DB_PASSWORD` | Remote database password |

Redeploy after adding the variables. The settings in `.env.example` are only for local Laragon and must not be copied as production credentials.

### Vercel persistence requirements

The current dashboard saves uploaded images and music under `uploads/`, and PHP uses file-based sessions. Files written inside a Vercel container are not durable or shared across instances. Before relying on dashboard uploads and admin login in production, move uploads to object storage (for example Vercel Blob) and sessions to a shared store (for example Redis or a database). RSVP and invitation data persist in the remote MySQL database. Local media already in `uploads/` is bundled with the deployment, but changes uploaded on the live site will not persist yet.

Vercel currently documents Docker containers as its PHP deployment approach. The older `vercel-php` community runtime is not required by this project.
