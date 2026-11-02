# BlablaTrack Deployment Guide

## Requirements

- Windows/Linux hosting dengan PHP 8.3+ dan extensions `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `json`, dan `xml`.
- Composer 2.x.
- Web server yang dapat menjalankan Laravel melalui `public/`.
- Storage writable untuk `storage/` dan `bootstrap/cache/`.
- SQLite file backup yang terjadwal.

## Backend Preparation

```bash
cd backend
composer install --no-dev --optimize-autoloader
copy .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Atur `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.example.test
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
FRONTEND_URL=https://app.example.test
```

Jangan commit `.env`, token, password, atau database production ke repository.

## Frontend

Serve direktori `frontend/` sebagai static site. Sesuaikan `API_BASE_URL` pada `frontend/js/api/client.js` dengan URL API production dan gunakan HTTPS.

## Backup

Hentikan atau koordinasikan proses write sebelum menyalin `database.sqlite`. Simpan backup terenkripsi dan uji pemulihan secara berkala. Backup attachment storage juga wajib dilakukan.

## Current Readiness

Aplikasi belum dinyatakan production-ready sampai checklist keamanan, composer audit, browser testing, dan konfigurasi production selesai.
