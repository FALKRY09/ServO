# BlablaTrack Security Checklist

## Passed / Implemented

- Password menggunakan hashing Laravel.
- Sanctum token digunakan untuk API.
- Password dan token hash disembunyikan dari User response.
- Role dan ownership diperiksa di backend.
- Akun nonaktif ditolak login dan token existing-nya ditolak.
- Input API divalidasi.
- Eloquent digunakan untuk query, bukan SQL string dari input pengguna.
- Attachment dibatasi MIME/ekstensi dan ukuran 10 MB serta download melalui authorization.
- CORS menggunakan `FRONTEND_URL`, bukan wildcard.
- Login diberi throttle `10 requests per minute`.
- `.env` masuk `.gitignore`.

## Required Before Production

- Set `APP_DEBUG=false`.
- Generate `APP_KEY` baru untuk environment production.
- Ganti/hapus akun seed `admin@example.com` dan password development.
- Gunakan HTTPS dan origin frontend production yang spesifik.
- Jalankan `composer audit` pada environment yang memiliki akses Packagist.
- Review token expiration/revocation policy dan logging.
- Tambahkan security headers, monitoring, backup, dan restore test.
- Pastikan storage attachment tidak dapat diakses langsung tanpa route authorization.
