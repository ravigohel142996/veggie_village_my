# Veggie Village - Docker + Render Deployment Guide

This repository is configured for direct deployment on Render using Docker with **PHP 8.2 + Apache**.

## Deployment-ready files

- `Dockerfile` (PHP 8.2 Apache image + required extensions)
- `docker/apache-vhost.conf` (Apache vhost with `AllowOverride All`)
- `docker/start-apache.sh` (bind Apache to Render `$PORT`)
- `.htaccess` (rewrite support and auth header forwarding)
- `.dockerignore` (clean build context)
- `render.yaml` (Render Blueprint using Docker)

## Required environment variables

Configure these in Render service settings (or use Blueprint sync):

- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `APP_URL` (example: `https://your-service-name.onrender.com`)
- `SMTP_USER`
- `SMTP_PASS`
- `SMTP_FROM`
- `APP_DEBUG` (`false` in production)

## Render deployment

1. Push the repository to GitHub.
2. In Render, create a **Blueprint** service from the repository.
3. Render will detect `render.yaml` and build using `Dockerfile`.
4. Set required env vars.
5. Deploy.

## Local Docker run

From repository root:

```bash
docker build -t veggie-village .
docker run --rm -p 10000:10000 \
  -e PORT=10000 \
  -e DB_HOST=host.docker.internal \
  -e DB_USER=root \
  -e DB_PASS= \
  -e DB_NAME=vaggie_village \
  veggie-village
```

Open: `http://localhost:10000`

## Database setup

The app now performs an automatic database bootstrap check at startup and initializes the database when it is empty:

- creates the database (from `DB_NAME`) if it does not exist
- checks whether tables exist
- imports `veggie_village_db.sql` automatically when no tables are present (and also supports the existing `veggei_village_db.sql` filename)

This works for Render and Railway deployments without manual SQL import.

## Notes

- Database credentials are read via `getenv()` in `backends/config.php`.
- Production-safe exception handling is configured in `backends/bootstrap.php`.
- Admin uploads continue using the `images/` directory, now writable in container runtime.
