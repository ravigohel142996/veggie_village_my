# Veggie Village - Render Deployment Guide

This project is now deployment-ready for Render using PHP and MySQL.

## Final folder structure

```
veggie_village/
├── admin/
├── api/
├── backends/
├── chunks/
├── css/
├── images/
├── js/
├── composer.json
├── render.yaml
├── index.php
└── README.md
```

## Environment variables

Set these in Render (**Dashboard -> Service -> Environment**):

- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `APP_URL` (example: `https://your-service-name.onrender.com`)
- `SMTP_USER`
- `SMTP_PASS`
- `SMTP_FROM`

## Exact Render settings

- **Runtime:** `PHP`
- **Build Command:** `composer install --no-dev --optimize-autoloader`
- **Start Command:** `php -S 0.0.0.0:$PORT -t .`
- **Root Directory:** repository root

If using Blueprint deploy from GitHub, Render will read `render.yaml` automatically.

## Local verification command

Run from project root:

```bash
php -S 0.0.0.0:10000 -t .
```

Then open: `http://localhost:10000`

## Deployment readiness summary

- Database config moved to environment variables (`getenv`)
- DB connection includes production error logging/handling
- Hardcoded localhost/XAMPP-style URL dependencies removed
- Asset/API/app paths updated for root-domain deployment
- Render runtime and start command configured

## Database setup note

Import `veggei_village_db.sql` into your MySQL instance, then match Render env vars to that database.
