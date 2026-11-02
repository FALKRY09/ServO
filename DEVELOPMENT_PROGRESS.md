# BlablaTrack — Development Progress

## Ticket detail actions

- Status update, authenticated attachment download, and assignment dropdown were integrated end-to-end.
- Added GET /api/users/assignees for active Petugas selection.
- Ticket detail now loads assignment/history relations and displays the active assignee.
- Validation: Laravel tests 16 passed (46 assertions); JavaScript syntax checks passed.

## Status Fase

| Fase | Status |
|---|---|
| Fase 1 — Analisis kebutuhan dan dokumentasi | Selesai, menunggu konfirmasi rancangan |
| Fase 2 — Persiapan lingkungan | Belum dimulai |
| Fase 3 — Database design dan migration | Belum dimulai |
| Fase 4 — Authentication dan authorization | Belum dimulai |
| Fase 5 — User dan master data | Belum dimulai |
| Fase 6 — Ticket management | Belum dimulai |
| Fase 7 — Assignment, communication, notification | Belum dimulai |
| Fase 8 — Dashboard dan reporting | Belum dimulai |
| Fase 9 — Frontend dan integrasi API | Belum dimulai |
| Fase 10 — Testing, security review, deployment | Belum dimulai |

## Fase 1 — Selesai

### Pekerjaan

- Memeriksa workspace: belum ada proyek Laravel atau struktur aplikasi.
- Menyusun overview, functional/non-functional requirements, role matrix, kebutuhan terbuka, dan rancangan database awal.
- Menetapkan bahwa belum ada migration, controller, model, endpoint, frontend, atau dependensi yang dibuat.

### File dibuat

- `PROJECT_OVERVIEW.md`
- `REQUIREMENTS.md`
- `DATABASE_DESIGN.md`
- `DEVELOPMENT_PROGRESS.md`

### Pengujian

- Pemeriksaan direktori workspace selesai.
- Belum ada automated test karena implementasi belum dimulai.

### Masalah/konfirmasi terbuka

- Konflik nama BlablaTrack vs ServiTrack.
- Detail status workflow, SLA, permission, attachment, notification, rating, dan kebijakan retensi perlu disetujui.

### Tahap berikutnya

Fase 2 hanya dimulai setelah pengguna menyetujui hasil Fase 1 dan memberikan keputusan atas pertanyaan terbuka yang relevan.

## Fase 2 - Pemeriksaan Lingkungan

### Hasil pemeriksaan

- PHP 8.5.9 tersedia dan memenuhi syarat PHP 8.3+.
- Composer 2.10.1, Node.js 26.5.0, dan npm 11.17.0 tersedia.
- Ekstensi PHP `pdo_sqlite` dan `sqlite3` aktif.
- Ekstensi pendukung `mbstring`, `openssl`, `fileinfo`, `json`, dan `xml` aktif.
- SQLite CLI (`sqlite3`) tidak tersedia, tetapi tidak wajib jika database diakses melalui PHP/Laravel.

### Isu lingkungan

- PHP dan Composer ditemukan melalui `C:\\Users\\Advan\\.config\\herd\\bin`. Herd dilarang dalam spesifikasi proyek. Laravel belum dipasang dan executable tersebut belum digunakan untuk membuat aplikasi.
- Workspace belum memiliki `composer.json`, sehingga pemeriksaan dependensi Laravel belum dapat dilakukan.

### File dibuat/diubah

- Tidak ada file aplikasi baru.
- `DEVELOPMENT_PROGRESS.md` diperbarui.

### Pengujian

- Pemeriksaan versi tool berhasil.
- Pemeriksaan ekstensi PHP berhasil.
- `composer check-platform-reqs` belum dapat dilakukan karena proyek Laravel belum ada.

### Keputusan yang diperlukan sebelum Fase 3

1. Sediakan PHP dan Composer yang tidak berasal dari Herd, atau setujui pemakaian binary tersebut sementara untuk development.
2. Setelah keputusan lingkungan dan persetujuan rancangan database, fase berikutnya dapat membuat migration.

## Fase 3 - Validasi Rancangan Database (Prasyarat)

### Hasil

- Relasi, foreign key, index utama, aturan nullable, dan strategi soft-disable telah ditinjau.
- Tidak ditemukan kebutuhan tabel tambahan di luar daftar yang disepakati.
- Ambiguitas SLA dan aturan status masih memengaruhi rancangan final.

### Status

Migration belum dibuat karena rancangan database belum disetujui secara eksplisit dan sumber PHP/Composer masih menggunakan Herd.

## Fase 3 - Implementasi Database

### Pekerjaan selesai

- Membuat proyek Laravel 13 di `backend/`.
- Menambahkan migration domain `backend/database/migrations/2026_11_02_000003_create_helpdesk_tables.php`.
- Membuat tabel roles, permissions, role_permissions, departments, categories, priorities, sla_policies, tickets, ticket_assignments, ticket_messages, ticket_attachments, ticket_histories, notifications, ticket_ratings, dan activity_logs.
- Menambahkan relasi foreign key, index, unique constraint, nullable rule, dan strategi restrict/null-on-delete sesuai rancangan yang disetujui.
- Menambahkan kolom role, departemen, status aktif, dan login terakhir pada users melalui migration.
- Menggunakan SQLite melalui `backend/database/database.sqlite`.

### Pengujian

- `php artisan migrate --force` berhasil.
- `php artisan migrate:status` menunjukkan seluruh migration berstatus `Ran`.

### Catatan

- Migration dibuat sebagai satu migration domain agar urutan foreign key aman terhadap migration bawaan Laravel.
- Model, seeder, controller, endpoint, Sanctum, dan frontend belum dibuat karena berada pada fase berikutnya.

## Fase 4 - Authentication dan Authorization

### Pekerjaan selesai

- Memasang Laravel Sanctum.
- Menambahkan migration `personal_access_tokens` dan menjalankannya pada SQLite.
- Menambahkan `HasApiTokens` pada model User.
- Menambahkan model Role, Permission, dan Department beserta relasi dasar.
- Menambahkan endpoint login, profil pengguna, dan logout:
  - `POST /api/auth/login`
  - `GET /api/auth/me`
  - `POST /api/auth/logout`
- Menambahkan middleware role dengan alias `role` untuk pembatasan akses backend.
- Menolak login untuk akun nonaktif dan menyimpan `last_login_at`.

### Pengujian

- `php artisan migrate --force` berhasil.
- `php artisan route:list --path=api` menampilkan tiga endpoint autentikasi.
- `php artisan test --compact` berhasil: 2 test, 2 assertion.

### Catatan

- Endpoint pendaftaran publik tidak dibuat; pembuatan pengguna akan dilakukan melalui modul administrasi.
- Seeder role/admin dan pengujian API autentikasi end-to-end akan disempurnakan pada tahap user management/testing.

## Fase 5 - User dan Master Data Management

### Pekerjaan selesai

- Menambahkan model `Category` dan `Priority`.
- Menambahkan CRUD administratif untuk user, department, category, dan priority.
- Menambahkan validasi request, unique email/name, validasi foreign key, password hashing, dan pagination.
- Membatasi endpoint master data dan user management dengan middleware `role:Admin`.
- Menambahkan seeder role default, department, category, priority, dan akun admin lokal.

### Endpoint

- `GET|POST /api/users`, `PUT /api/users/{user}`
- `GET|POST /api/departments`, `PUT /api/departments/{department}`
- `GET|POST /api/categories`, `PUT /api/categories/{category}`
- `GET|POST /api/priorities`, `PUT /api/priorities/{priority}`

### Pengujian

- `php artisan db:seed --force` berhasil.
- `php artisan route:list --path=api` menampilkan 15 route.
- `php artisan test --compact` berhasil: 4 test, 7 assertion.

### Catatan keamanan

- Akun admin seed hanya untuk development lokal: `admin@example.com` / `password123`. Password wajib diganti atau seed diubah sebelum deployment.
- Penghapusan user/master data belum disediakan; data dinonaktifkan melalui `is_active`.

## Fase 6 - Ticket Management

### Pekerjaan selesai

- Menambahkan model `Ticket` dan `TicketHistory`.
- Menambahkan pembuatan tiket oleh pengguna terautentikasi.
- Menambahkan daftar tiket dengan pembatasan akses:
  - Admin/Koordinator dapat melihat seluruh tiket.
  - Pelapor hanya melihat tiket miliknya.
- Menambahkan detail tiket beserta riwayat.
- Menambahkan perubahan status dengan validasi transisi:
  `Open`, `Assigned`, `In Progress`, `Pending`, `Resolved`, `Closed`.
- Mencatat pembuatan dan setiap perubahan status ke `ticket_histories`.
- Menambahkan timestamp `resolved_at`, `closed_at`, dan `last_activity_at`.

### Endpoint

- `GET /api/tickets`
- `POST /api/tickets`
- `GET /api/tickets/{ticket}`
- `PATCH /api/tickets/{ticket}/status`

Tambahan Fase 7:

- `PUT /api/tickets/{ticket}` untuk pembaruan tiket yang diizinkan.
- `GET /api/tickets` mendukung `search`, `status`, `priority_id`, `category_id`, dan `page`.

### Pengujian

- `php artisan route:list --path=api/tickets` menampilkan 4 route.
- `php artisan test --compact` berhasil: 6 test, 11 assertion.

### Batasan fase

- Assignment, komunikasi, lampiran, notifikasi, dan akses petugas akan dibuat pada Fase 7.

## Fase 7 - Assignment, Communication, and Notification

### Pekerjaan selesai

- Menambahkan model assignment, message, dan notification.
- Admin/Koordinator dapat menugaskan tiket kepada user ber-role Petugas aktif.
- Assignment aktif sebelumnya dinonaktifkan dengan `unassigned_at`.
- Status tiket berubah menjadi `Assigned` dan histori assignment dicatat.
- Petugas menerima notifikasi in-app ketika ditugaskan.
- Pelapor dan staff berwenang dapat membaca/mengirim pesan tiket.
- Pesan internal hanya boleh dibuat Admin/Koordinator.
- Pengguna dapat melihat notifikasi sendiri dan menandainya telah dibaca.

### Endpoint

- `POST /api/tickets/{ticket}/assignments`
- `GET|POST /api/tickets/{ticket}/messages`
- `GET /api/notifications`
- `PATCH /api/notifications/{notification}/read`

Penyempurnaan Fase 8:

- `POST /api/assignments/{assignment}/accept` untuk penerimaan assignment oleh Petugas.
- `GET /api/notifications/unread-count` untuk badge notifikasi.
- Notifikasi dibuat untuk tiket baru, perubahan status, dan pesan baru kepada pengguna relevan.
- Activity log dibuat untuk pembuatan tiket, perubahan status, assignment, penerimaan assignment, dan pesan.
- Attachment tetap menggunakan validasi file dan pemeriksaan akses tiket.

### Pengujian Fase 8

- Seluruh regression test berhasil: 16 test, 46 assertion.
- Route assignment acceptance dan unread notification berhasil terdaftar.

### Pengujian

- Route assignment, message, dan notification berhasil terdaftar.
- `php artisan test --compact` berhasil: 6 test, 11 assertion.

### Catatan

- Upload attachment belum diimplementasikan; kebijakan ukuran, MIME type, disk, dan retensi masih diperlukan.
- Akses petugas untuk melihat tiket yang ditugaskan perlu penyempurnaan pada iterasi berikutnya.

### Penyelesaian Fase 7

- Petugas aktif kini dapat melihat tiket yang sedang ditugaskan kepadanya.
- Petugas dapat membaca dan mengirim pesan pada tiket yang ditugaskan.
- Menambahkan upload attachment ke disk lokal Laravel.
- Validasi attachment: wajib file, maksimum 10 MB, dan tipe terbatas (`jpg`, `png`, `pdf`, `doc/docx`, `xls/xlsx`, `txt`, `zip`).
- Menambahkan daftar attachment dan download attachment dengan pemeriksaan akses tiket.
- Pengujian regresi tetap berhasil: 6 test, 11 assertion.

## Fase 8 - Dashboard dan Reporting

### Pekerjaan selesai

- Menambahkan endpoint ringkasan dashboard untuk Admin dan Koordinator.
- Mendukung filter periode `from` dan `to`.
- Menyediakan total tiket, distribusi status, prioritas, kategori, jumlah resolved, dan jumlah closed.
- Agregasi dilakukan melalui query database, bukan mengambil seluruh tiket ke memory.

### Endpoint

- `GET /api/dashboard/summary`

Parameter opsional: `from=YYYY-MM-DD` dan `to=YYYY-MM-DD`.

### Pengujian

- Route dashboard berhasil terdaftar.
- `php artisan test --compact` berhasil: 6 test, 11 assertion.

### Catatan

- Visual dashboard dan export laporan belum dibuat; keduanya termasuk integrasi frontend/reporting lanjutan.

## Fase 9 - Dashboard and Reporting

### Pekerjaan selesai

- Dashboard summary kini menyesuaikan role Admin, Koordinator, Petugas, dan Pelapor.
- Admin mendapat total user aktif, jumlah Petugas aktif, dan aktivitas terbaru.
- Koordinator mendapat tiket belum ditugaskan, in-progress, pending, dan placeholder SLA overdue yang eksplisit `null`.
- Petugas mendapat ringkasan tiket assignment aktif.
- Pelapor mendapat ringkasan tiket miliknya.
- Menambahkan laporan tiket dengan filter periode, status, prioritas, kategori, departemen, dan petugas.
- Mendukung agregasi `group_by` menggunakan query database.

### Endpoint

- `GET /api/dashboard/summary`
- `GET /api/reports/tickets`

### Pengujian

- Route laporan berhasil terdaftar.
- Regression test berhasil: 16 test, 46 assertion.

### Catatan

- SLA overdue belum dihitung karena kalender kerja, hari libur, pause, dan escalation belum memiliki aturan final.

## Fase 10 - UI/UX Design

### Pekerjaan selesai

- Menentukan prinsip UX, layout application shell, pola interaksi, responsive behavior, dan accessibility.
- Menentukan navigasi berdasarkan empat role.
- Menentukan design tokens: warna, status, tipografi, spacing, radius, dan shadow.
- Mendokumentasikan komponen visual utama.
- Mendokumentasikan user flow login, pembuatan tiket, penugasan, penanganan, penyelesaian, dan rating.
- Mendokumentasikan sitemap dan state loading/empty/error/success/unauthorized.

### File dibuat

- `UI_UX_GUIDELINES.md`
- `DESIGN_SYSTEM.md`
- `USER_FLOW.md`

### Batasan

- Tidak ada kode frontend dibuat.
- Tidak ada perubahan backend atau database.
- Mockup gambar high-fidelity belum dibuat; dokumentasi ini menjadi blueprint implementasi frontend berikutnya.

## Fase 11 - Frontend Development and API Integration

### Pekerjaan selesai

- Mengembangkan frontend terpisah berbasis HTML, CSS, Bootstrap 5, Vanilla JavaScript, dan Fetch API.
- Mengimplementasikan design system dasar: brand, warna, panel, card metric, sidebar, badge, form, dan responsive layout.
- Mengimplementasikan login/logout dengan token `sessionStorage`.
- Mengimplementasikan role-aware navigation dasar.
- Mengintegrasikan dashboard summary dan daftar tiket ke API nyata.
- Menambahkan pencarian tiket dan form pembuatan tiket.
- Menambahkan loading/error/empty feedback dasar dan escaping output API.

### Pengujian

- Sintaks `app.js`, `client.js`, dan `dom.js` berhasil diperiksa dengan Node.js.
- Frontend server merespons HTTP 200 pada `index.html`.
- Backend test suite tetap lulus: 16 test, 46 assertion.

### Batasan

- UI management master data, detail tiket, assignment, komunikasi, attachment, notifikasi penuh, laporan, dan profil belum selesai.
- Tidak ada perubahan API atau backend pada fase ini.

## Frontend Routing 404 Fix

### Root cause

The router used `history.pushState()` with `location.pathname`. Live Server treated `/departments`, `/notifications`, and similar paths as physical files and returned HTTP 404.

### Fix

- `router.js` now uses `location.hash`, `hashchange`, and hash navigation.
- Routes are opened as `http://127.0.0.1:5500/#/departments`.
- Browser Back/Forward is handled by hash history.
- Ticket detail routes `/tickets/{id}` remain supported.
- Unknown routes resolve to `/404` inside the SPA.
- API base URL remains `http://127.0.0.1:8000/api`.

### Verification

- All frontend JavaScript files passed `node --check`.
- `location.pathname` is no longer used as the frontend route source.
- No literal `href="#"` or old BlablaTrack branding found in frontend.
- Live Server root `index.html` returned HTTP 200.

## UI/UX Redesign - Modern Enterprise Dashboard

### Audit

- Existing UI had a functional shell but minimal visual hierarchy, duplicated CSS responsibilities, compact one-line components, and limited responsive treatment.
- Backend, API, SQLite, and authentication were not changed.

### Changes

- Rebuilt design tokens around navy/deep blue, slate, light gray, white surfaces, and restrained semantic colors.
- Redesigned topbar with brand, avatar, role context, notification affordance, and logout action.
- Redesigned sidebar active state, grouping, spacing, and responsive horizontal navigation.
- Added consistent page header, breadcrumb, panels, metric cards, table treatment, forms, focus states, modal styling, badges, empty state, and loading state.
- Improved responsive behavior for tablet and mobile layouts, including controlled table overflow.
- Kept existing API calls, hash routing, and authentication behavior intact.

### Verification

- All JavaScript files pass `node --check`.
- Frontend smoke check returns HTTP 200.
- No legacy BlablaTrack branding remains in frontend files.

## Navbar User Profile Alignment Fix

- Added `margin-left:auto` to `.topbar-actions`, pushing notification and profile controls to the far right.
- Preserved left-side ServiTrack branding and reserved the center area for page context.
- Kept the order: notification → avatar → user name → role → logout action.
- Bound the role label to the authenticated user instead of the static workspace text.
- On mobile, the avatar remains visible while secondary profile text collapses to avoid collisions.
- JavaScript syntax checks passed and frontend HTTP smoke check returned 200.

## Frontend Completion & Navigation Repair - Kelompok 4 Operations UI

### Pekerjaan selesai

- Menambahkan detail tiket dengan komunikasi pesan.
- Menambahkan perubahan status staff melalui API.
- Menambahkan upload dan daftar attachment dengan endpoint backend.
- Menambahkan form assignment Admin/Koordinator.
- Menambahkan halaman notifikasi dan aksi tandai dibaca.
- Menambahkan unread count notifikasi.
- Menambahkan laporan tiket berdasarkan status.
- Menambahkan profil pengguna dasar.

### Pengujian kelompok 4

- Sintaks `app.js` berhasil.
- Frontend smoke check HTTP 200 berhasil.
- Tidak ditemukan branding lama atau navigasi `href="#"` semu.

### Belum selesai

- Edit/toggle master data dari frontend.
- Penerimaan assignment oleh Petugas dari frontend.
- Laporan dengan filter lanjutan dan export.

## Critical Frontend Fix - Modular Structure and History Routing

### Perubahan langsung

- Menambahkan struktur CSS modular: `layout.css`, `components.css`, `responsive.css`.
- Menambahkan component modules: sidebar, navbar, modal, table, notification.
- Menambahkan utility auth dan page modules untuk dashboard, tickets, detail, create, master data, assignment, communication, notifications, reports, activity logs, dan profile.
- Mengganti navigasi hash menjadi History API dengan `pushState` dan `popstate`.
- Memastikan route tidak dikenal diarahkan ke halaman 404.
- Mengubah title dan branding menjadi ServiTrack/ST.
- Memastikan seluruh JavaScript frontend lulus syntax check.

### Batasan yang terverifikasi

- Activity Log belum memiliki endpoint read backend.
- Assignment acceptance Petugas membutuhkan assignment ID yang belum dikirim detail tiket.
- Download attachment perlu authenticated blob flow.
- Python static server tidak menyediakan fallback untuk direct refresh pada nested History API path; production static server perlu rewrite semua route ke `index.html`.

## Frontend Completion - Kelompok 5 Final Operations Review

### Selesai

- Assignment creation, attachment upload/list, notifications, unread count, status report, and profile UI are connected to existing API routes.
- JavaScript syntax and frontend HTTP smoke checks pass.

### Backend-dependent gaps

- Petugas acceptance UI needs an assignment ID in the ticket detail payload or an assignment list endpoint.
- Secure attachment download needs an authenticated blob-download helper; raw file paths are not exposed as links.
- Master edit forms, advanced report filters, and export are not implemented in the current UI.

## Frontend Completion & Navigation Repair - Kelompok 3 Ticket UI

### Pekerjaan selesai

- Menambahkan route detail tiket `/tickets/{id}` dan link dari daftar tiket.
- Menghubungkan detail tiket ke endpoint tiket dan messages.
- Menambahkan form komunikasi/informasi tambahan.
- Menambahkan form perubahan status untuk role staff.
- Menampilkan status, kategori, prioritas, isi pesan, dan timestamp.
- Menjaga output API melalui `escapeHtml`.

### Pengujian kelompok 3

- Sintaks `app.js` berhasil.
- Frontend smoke check HTTP 200 berhasil.
- Tidak ditemukan branding lama atau navigasi `href="#"`.

### Belum selesai

- Assignment UI, attachment UI, notifikasi penuh, laporan, profil, dan edit/toggle master data.

## Frontend Completion & Navigation Repair - Kelompok 1

### Audit

- Backend diverifikasi memiliki 44 route API.
- Frontend sebelumnya hanya memiliki `index.html`, satu `app.js`, API client, dan CSS dasar.
- Tidak ditemukan UI page module atau router sebelumnya.

### Perubahan

- Branding frontend diubah menjadi ServiTrack dengan logo `ST`.
- Menambahkan hash router dengan browser back/forward dan fallback 404.
- Menghapus navigasi `href="#"` semu; menu menggunakan route nyata.
- Menambahkan navigasi berbasis role.
- Memecah routing dan layout ke modul terpisah.
- Token frontend menggunakan key `servitrack_token` di `sessionStorage`.
- Menjaga unsupported page tetap eksplisit tanpa data dummy.

### Pengujian kelompok 1

- JavaScript syntax check berhasil untuk app, router, layout, API client, dan utility.
- Branding lama dan `href="#"` tidak lagi ditemukan di frontend.
- Frontend smoke check HTTP 200 berhasil.

### Status

Kelompok audit, routing, branding, dan layout selesai. Implementasi halaman per role berikutnya menunggu persetujuan sebelum perubahan besar lanjutan.

## Frontend Completion & Navigation Repair - Kelompok 2 Admin

### Pekerjaan selesai

- Menghubungkan menu Admin ke halaman `/users`, `/departments`, `/categories`, `/priorities`, dan `/sla-policies`.
- Menambahkan daftar data dari API nyata, pencarian, empty state, dan form tambah.
- Menggunakan konfigurasi resource bersama agar pola tabel dan form konsisten.
- Menambahkan konversi field numerik sebelum request API.

### Pengujian kelompok 2

- Sintaks `app.js` dan `router.js` berhasil.
- Branding lama dan navigasi semu tetap tidak ditemukan.
- Frontend smoke check HTTP 200 berhasil.

### Belum selesai

- Edit/toggle data, detail tiket, assignment, komunikasi, attachment, notifikasi, laporan, profil, dan dashboard role khusus.

## Fase 12 - Testing, Security, and Deployment

### Pekerjaan

- Menjalankan regression test backend dan pemeriksaan sintaks PHP/JavaScript.
- Memeriksa status migration dan registrasi route API.
- Menambahkan throttle login `10 requests per minute`.
- Membuat laporan testing, security checklist, dan deployment guide.

### Hasil

- 16 test passed, 46 assertions.
- 5 migration berstatus `Ran`.
- PHP dan JavaScript syntax checks passed.
- Frontend smoke check HTTP 200.
- Composer audit tertunda karena koneksi Packagist tidak tersedia.

### Bug/risiko tersisa

- `APP_DEBUG=true` masih untuk local development.
- Akun seed development dan password default harus dihapus/diganti.
- Frontend belum mencakup semua halaman fase 11.
- Browser automation, load test, dan dedicated tests untuk seluruh event belum tersedia.

### Status kesiapan

Belum production-ready. Deployment hanya boleh dilakukan setelah required security checklist dan deployment preparation selesai.

## Fase 9 - Frontend dan Integrasi API

### Pekerjaan selesai

- Membuat frontend terpisah di direktori `frontend/`.
- Menambahkan halaman HTML dasar dengan Bootstrap 5.
- Menambahkan client Fetch API terpusat.
- Menambahkan alur login, logout, pemuatan profil, dashboard summary, dan daftar tiket.
- Token API disimpan di `sessionStorage`, bukan localStorage.
- Menambahkan konfigurasi CORS backend untuk route API.

### File utama

- `frontend/index.html`
- `frontend/css/app.css`
- `frontend/js/api/client.js`
- `frontend/js/app.js`
- `backend/config/cors.php`

### Pengujian

- Sintaks `frontend/js/app.js` berhasil diperiksa dengan Node.js.
- Sintaks `frontend/js/api/client.js` berhasil diperiksa dengan Node.js.
- Test backend tetap berhasil: 6 test, 11 assertion.

### Catatan

- Frontend masih berupa preview operasional dasar, belum mencakup form pembuatan tiket, assignment, pesan, attachment, dan admin management.
- `API_BASE_URL` perlu disesuaikan bila backend dijalankan pada host/port berbeda.

## Fase 10 - Testing, Security Review, and Deployment Readiness

### Review dan perbaikan

- Membatasi Pelapor agar hanya dapat mengonfirmasi `Resolved` menjadi `Closed`.
- Memastikan assignment hanya dapat dibuat oleh Admin/Koordinator dan hanya untuk Petugas aktif.
- Memastikan akses tiket, pesan, dan attachment mengikuti requester atau assignment aktif.
- Membatasi CORS ke `FRONTEND_URL`, dengan default development `http://127.0.0.1:5500`.
- Memastikan password di-hash dan token tidak dikirim dalam respons selain saat login.

### Pengujian

- `php artisan test --compact` berhasil: 7 test, 13 assertion.
- `php artisan migrate:status` menunjukkan seluruh 5 migration berstatus `Ran`.
- Pemeriksaan sintaks frontend berhasil.

### Rekomendasi sebelum deployment

- Ganti `APP_KEY`, password admin seed, dan `FRONTEND_URL` sesuai environment deployment.
- Jangan memakai akun seed development pada production.
- Jalankan dengan `APP_DEBUG=false` dan HTTPS.
- Tambahkan rate limiting login, activity log terpusat, backup SQLite, dan pengujian upload file lebih luas.
- Dokumentasi API dan UI administratif masih perlu disempurnakan sebelum dianggap production-ready.

### Handoff dokumentasi

- Menambahkan `API_DOCUMENTATION.md` berisi base URL, autentikasi, endpoint, akses role, status workflow, dan format error.

## Fase 4 - Backend Architecture

### Pekerjaan selesai

- Meninjau struktur Laravel dan mempertahankan pemisahan `routes`, `controllers`, `models`, `middleware`, dan `config`.
- Menetapkan prefix API `/api` dengan penamaan resource konsisten dan HTTP method sesuai fungsi.
- Menambahkan penanganan JSON terpusat untuk validation error serta HTTP error pada route API.
- Menetapkan format validation error: `message` dan `errors`.
- Mengonfigurasi CORS menggunakan environment `FRONTEND_URL`.
- Menggunakan pagination Laravel pada endpoint list dengan parameter `page`.
- Tidak menambahkan service layer atau abstraksi baru karena controller yang ada masih sederhana.

### Pengujian

- `php artisan route:list --path=api` menampilkan 28 route.
- Validasi login menghasilkan JSON HTTP 422 yang konsisten.
- Route API yang tidak ditemukan menghasilkan JSON HTTP 404.
- `php artisan test --compact` berhasil: 9 test, 18 assertion.

### Batasan

- Tidak ada modul bisnis baru, migration baru, atau perubahan database pada fase ini.

## Fase 5 - Authentication and Authorization

### Pekerjaan selesai

- Menambahkan pemeriksaan middleware akun aktif pada seluruh route terproteksi.
- Memastikan token Sanctum milik akun yang kemudian dinonaktifkan tidak dapat digunakan.
- Memastikan role middleware memeriksa role dan status akun di backend.
- Menambahkan cast boolean untuk `users.is_active` dan cast datetime untuk `last_login_at`.
- Melengkapi UserFactory agar menghasilkan akun aktif secara eksplisit.
- Memperbarui dokumentasi aturan token dan akun nonaktif.

### Pengujian

- Login valid untuk Admin, Koordinator, Petugas, dan Pelapor.
- Login invalid dan akun nonaktif ditolak.
- Token tidak valid dan request tanpa autentikasi ditolak.
- Token akun yang dinonaktifkan setelah login ditolak.
- Pembatasan role dan akses lintas pengguna diuji.
- `php artisan test --compact` berhasil: 12 test, 33 assertion.

### Catatan keamanan

- Logout menghapus token Sanctum yang sedang digunakan.
- Password tidak dikembalikan dalam response karena hidden attribute model User.
- Tidak ada registrasi publik; pembuatan user dilakukan melalui administrasi.

## Fase 6 - Master Data and User Management

### Pekerjaan selesai

- Melengkapi user management: daftar, detail, tambah, edit, pencarian/filter, dan toggle aktif/nonaktif.
- Melengkapi department management: daftar, detail, CRUD, pencarian, dan daftar anggota department.
- Melengkapi category management: daftar, detail, CRUD, pencarian, filter status, dan toggle aktif/nonaktif.
- Melengkapi priority management: daftar, detail, CRUD, pencarian, filter status, dan toggle aktif/nonaktif.
- Menambahkan model dan CRUD SLA policy dengan target response/resolution, relasi optional ke priority/category, dan filter nama.
- Seluruh endpoint master data dibatasi middleware `role:Admin`.
- Tidak menyediakan hard delete agar data yang direferensikan tetap aman.

### Pengujian

- Pengujian duplicate category menghasilkan HTTP 422.
- Pengujian non-Admin menghasilkan HTTP 403.
- Pengujian filter user dan pagination berhasil.
- `php artisan test --compact` berhasil: 15 test, 42 assertion.

### Catatan

- SLA business calendar, hari libur, pause, dan escalation belum dimodelkan karena belum disetujui sebagai kebutuhan rinci.

## Fase 2 - Audit Ulang Environment (2026-11-02)

### Hasil

- PHP CLI: 8.5.9, kompatibel dengan Laravel 13 dan syarat minimum PHP 8.3.
- Composer: 2.10.1.
- Laravel: 13.39.0 berhasil dijalankan melalui Artisan.
- SQLite: `DB_CONNECTION=sqlite`, file `backend/database/database.sqlite` tersedia.
- Ekstensi aktif: `PDO`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `fileinfo`, `json`, `tokenizer`, dan `xml`.
- `php artisan about` berhasil tanpa error konfigurasi database.
- `php artisan migrate:status` menunjukkan seluruh migration berstatus `Ran`.
- SQLite CLI `sqlite3` tidak tersedia di PATH; akses SQLite PHP tetap berfungsi.

### Kendala yang menghentikan sertifikasi penuh Fase 2

- PHP dan Composer berasal dari `C:\\Users\\Advan\\.config\\herd\\bin`, sedangkan Herd dilarang oleh spesifikasi proyek.
- Workspace sudah berisi implementasi Fase 3 dan seterusnya dari pekerjaan sebelumnya. Tidak ada perubahan atau penghapusan dilakukan dalam audit ini.

### Status

Persyaratan teknis Laravel/SQLite terpenuhi, tetapi Fase 2 belum dapat dinyatakan sepenuhnya compliant sampai PHP dan Composer non-Herd tersedia atau larangan Herd dikonfirmasi ulang.
