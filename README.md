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
- `DB_PORT` (Railway MySQL is typically `3306`)
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
  -e DB_HOST=your-db-host \
  -e DB_USER=your-db-user \
  -e DB_PASS=your-db-password \
  -e DB_NAME=your-db-name \
  -e DB_PORT=3306 \
  veggie-village
```

Open: `http://localhost:10000`

## Database setup

The app connects to an existing MySQL database using environment variables:

- `DB_HOST`
- `DB_USER`
- `DB_PASS`
- `DB_NAME`
- `DB_PORT`

For Railway + Render deployments, ensure these values point to the Railway MySQL instance.

## Notes

- `backends/config.php` reads DB env vars and validates connectivity with `mysqli` using host/user/pass/name/port.
- `backends/connection-pdo.php` creates the shared PDO connection used throughout the app.
- Production-safe exception handling is configured in `backends/bootstrap.php`.
- Admin uploads continue using the `images/` directory, now writable in container runtime.
