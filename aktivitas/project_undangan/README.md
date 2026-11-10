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
