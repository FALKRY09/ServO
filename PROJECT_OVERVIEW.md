# BlablaTrack — Project Overview

## 1. Tujuan

BlablaTrack adalah sistem internal Helpdesk dan Ticketing untuk satu organisasi. Sistem membantu pegawai melaporkan masalah atau meminta layanan, koordinator menugaskan petugas, petugas menangani tiket, dan pelapor memantau serta mengonfirmasi penyelesaian.

## 2. Ruang Lingkup

### Termasuk

- Autentikasi dan otorisasi berbasis peran.
- Pengelolaan pengguna, departemen, kategori, prioritas, dan SLA.
- Pembuatan, penugasan, pemrosesan, komunikasi, dan penutupan tiket.
- Lampiran, notifikasi, rating, dashboard, laporan, dan activity log.
- REST API Laravel yang dikonsumsi frontend terpisah.

### Tidak termasuk

- Multi-organization atau multi-tenant.
- Tabel atau kolom `organization_id`.
- Frontend Blade, SPA framework, TypeScript, atau database server.
- Implementasi pada fase analisis ini.

## 3. Pengguna dan Peran

- **Admin:** administrasi pengguna, master data, SLA, seluruh tiket, laporan, dan aktivitas sistem.
- **Koordinator:** pemeriksaan tiket, prioritas, penugasan, pemantauan, dan laporan sesuai kewenangan.
- **Petugas:** menangani tiket yang ditugaskan, memperbarui status, memberi tanggapan, dan mencatat penyelesaian.
- **Pelapor:** membuat tiket, melihat tiket miliknya, memberi informasi tambahan, dan memberi rating setelah selesai.

## 4. Alur Tiket

`Open → Assigned → In Progress → Pending → In Progress → Resolved → Closed`

Tidak semua tiket harus melewati `Pending`. Transisi status, actor, waktu, dan catatan akan dicatat dalam riwayat. Aturan transisi final perlu dikonfirmasi sebelum implementasi.

## 5. Arsitektur Target

- **Backend:** Laravel 13, PHP 8.3+, RESTful JSON API, Sanctum, Eloquent, migrations, validation, middleware, dan service layer bila diperlukan.
- **Database:** SQLite satu file `database.sqlite`, dengan foreign key constraint aktif.
- **Frontend:** HTML5, CSS3, Vanilla JavaScript, Bootstrap 5, dan Fetch API di direktori terpisah.
- **Organisasi repository:** `backend/`, `frontend/`, dan `documentation/`.

## 6. Prinsip Teknis

Backend menjadi sumber kebenaran untuk autentikasi, otorisasi, validasi, status tiket, dan audit. Frontend tidak dipercaya untuk menegakkan kewenangan. Implementasi dilakukan bertahap dan setiap fase diverifikasi sebelum lanjut.

## 7. Risiko Awal

- Aturan transisi status dan kewenangan tiap peran belum cukup rinci.
- Definisi SLA, kalender kerja, zona waktu, dan eskalasi belum ditentukan.
- Kebijakan penyimpanan lampiran dan notifikasi belum ditentukan.
- Nama produk di instruksi tidak konsisten: BlablaTrack dan ServiTrack.
