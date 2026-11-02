# BlablaTrack Frontend Documentation

## Teknologi

- HTML5, CSS3, Bootstrap 5.3, Vanilla JavaScript ES modules, Fetch API.
- Tidak menggunakan React, Vue, Angular, TypeScript, Blade, atau Inertia.

## Menjalankan

Backend:

```powershell
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

Frontend:

```powershell
cd frontend
python -m http.server 5500 --bind 127.0.0.1
```

Buka `http://127.0.0.1:5500`. Base API berada di `frontend/js/api/client.js` dan default ke `http://127.0.0.1:8000/api`.

## Autentikasi

Token Sanctum disimpan di `sessionStorage` dengan key `blablatrack_token` dan dikirim sebagai Bearer token oleh API client. Saat logout, token dihapus dari session storage.

## Struktur

- `index.html`: app shell, login, dashboard, modal tiket.
- `css/app.css`: design tokens dan layout responsive.
- `js/api/client.js`: Fetch API terpusat, auth header, dan error parsing.
- `js/utils/dom.js`: escape output dan flash message.
- `js/app.js`: application state, dashboard, ticket list, and create ticket flow.
- `js/router.js`: hash routing, browser history, route fallback, and route access.
- `js/components/layout.js`: shared role-aware sidebar and active navigation.
- `js/utils/dom.js`: safe output escaping and flash message.

## Batasan Preview

Frontend saat ini mengimplementasikan login, logout, role-aware navigation, hash routing, dashboard summary, pencarian tiket, daftar/detail tiket, pembuatan tiket, komunikasi pesan, perubahan status untuk staff, attachment upload/list, assignment untuk Admin/Koordinator, notifikasi/read state, laporan status, profil, serta halaman Admin untuk daftar/pencarian/tambah master data. Edit/toggle master data dan penerimaan assignment Petugas masih memerlukan iterasi berikutnya.

Semua izin tetap diperiksa backend; navigasi role di frontend hanya membantu pengalaman pengguna.

## Current Operations UI

- Ticket detail: messages, status update, assignment creation, and attachment upload/list.
- Notifications: list, unread count, and mark as read.
- Reports: status aggregation.
- Profile: read-only current user information.

Backend limitations: the ticket detail response does not expose active assignment IDs, so the Petugas accept-assignment action cannot be safely wired yet. Attachment download requires an authenticated fetch/download flow rather than a raw public link and remains intentionally displayed as a protected backend resource.

## Live Server Routing

Use hash URLs such as `http://127.0.0.1:5500/#/dashboard` and `#/departments`. The router reads `location.hash`, listens to `hashchange`, and survives refreshes and browser Back/Forward without asking Live Server for `/departments` as a physical file.

## Critical Fix Audit

- History API routing is implemented through `pushState`, `popstate`, and route access checks.
- Sidebar actions use `data-route`; no literal `href="#"` navigation is used.
- Branding is ServiTrack/ST.
- Modular CSS, component, page, router, API, and utility directories are present.
- Activity log has no backend read endpoint, so no false frontend page was created for it.
