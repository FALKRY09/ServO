# BlablaTrack — Initial Database Design

> Ini adalah rancangan awal. Migration belum dibuat sampai rancangan dan keputusan terbuka disetujui.

## 1. Konvensi

- SQLite; primary key menggunakan `id` integer autoincrement.
- Timestamp menggunakan `created_at` dan `updated_at`; tanggal operasional memakai `datetime`.
- Status/role yang berkembang disimpan sebagai string tervalidasi di application layer atau tabel referensi bila diperlukan.
- Foreign key diaktifkan pada koneksi SQLite.
- Data master yang direferensikan sebaiknya dinonaktifkan (`is_active`) daripada dihapus.

## 2. Tabel dan Kolom Utama

### `roles`

`id`, `name` unique, `description` nullable, `created_at`, `updated_at`.

### `permissions`

`id`, `name` unique, `description` nullable, timestamps.

### `role_permissions`

`role_id` FK → `roles.id`, `permission_id` FK → `permissions.id`, timestamps; unique pair `(role_id, permission_id)`. Many-to-many role-permission.

### `departments`

`id`, `name` unique, `description` nullable, `is_active`, timestamps.

### `users`

`id`, `role_id` FK, `department_id` nullable FK, `name`, `email` unique, `password`, `is_active`, `last_login_at` nullable, timestamps. One role/department to many users. Role/departemen tidak dihapus jika masih direferensikan.

### `categories`

`id`, `name` unique, `description` nullable, `is_active`, timestamps.

### `priorities`

`id`, `name` unique, `level` integer, `response_target_minutes` nullable, `resolution_target_minutes` nullable, `is_active`, timestamps.

### `sla_policies`

`id`, `name`, `description` nullable, `priority_id` nullable FK, `category_id` nullable FK, target response/resolution dalam menit, `business_hours_only`, `is_active`, timestamps. Index pada kombinasi rule aktif. Detail kalender kerja/eskalasi masih terbuka.

### `tickets`

`id`, `ticket_number` unique, `requester_id` FK → users, `department_id` nullable FK, `category_id` FK, `priority_id` FK, `sla_policy_id` nullable FK, `title`, `description`, `status`, `resolved_at` nullable, `closed_at` nullable, `last_activity_at` nullable, timestamps. Index `(status)`, `(requester_id, status)`, `(category_id, priority_id)`, `(created_at)`.

### `ticket_assignments`

`id`, `ticket_id` FK, `assignee_id` FK → users, `assigned_by_id` FK → users, `assigned_at`, `accepted_at` nullable, `unassigned_at` nullable, `note` nullable, timestamps. One ticket to many assignment records; index `(assignee_id, unassigned_at)` and `ticket_id`.

### `ticket_messages`

`id`, `ticket_id` FK, `user_id` FK, `body`, `is_internal`, timestamps. One ticket to many messages; index `(ticket_id, created_at)`.

### `ticket_attachments`

`id`, `ticket_id` FK, `message_id` nullable FK, `uploaded_by_id` FK, `original_name`, `stored_name`, `path`, `disk`, `mime_type`, `size_bytes`, timestamps. Attachment dapat terkait tiket atau pesan. File fisik dihapus/ditangani sesuai kebijakan retensi.

### `ticket_histories`

`id`, `ticket_id` FK, `user_id` nullable FK, `event_type`, `from_status` nullable, `to_status` nullable, `old_values` nullable JSON/text, `new_values` nullable JSON/text, `note` nullable, timestamps. Menyimpan perubahan status, assignment, prioritas, dan event penting.

### `notifications`

`id`, `user_id` FK, `ticket_id` nullable FK, `type`, `title`, `body`, `read_at` nullable, `data` nullable JSON/text, timestamps. Index `(user_id, read_at)`.

### `ticket_ratings`

`id`, `ticket_id` FK unique, `user_id` FK, `rating` integer, `comment` nullable, timestamps. One-to-one dengan tiket untuk rating akhir; unique `ticket_id`.

### `activity_logs`

`id`, `user_id` nullable FK, `action`, `subject_type` nullable, `subject_id` nullable, `ip_address` nullable, `user_agent` nullable, `metadata` nullable JSON/text, timestamps. Index `(subject_type, subject_id)`, `(user_id, created_at)`, `(action)`.

## 3. Relasi dan Penghapusan

- Parent data master memakai `restrict`/`no action` bila masih direferensikan; gunakan `is_active` untuk penghentian penggunaan.
- Tiket ke pesan, assignment, attachment, history, notification, dan rating dapat memakai cascade hanya bila kebijakan retensi menyetujui penghapusan tiket. Rekomendasi awal: tiket tidak dihapus secara fisik.
- User yang menjadi actor/requester/assignee sebaiknya tidak dihapus; nonaktifkan akun dan pertahankan foreign key. FK actor nullable hanya untuk data historis yang harus tetap ada bila diperlukan.
- `role_permissions` dapat cascade ketika role/permission dihapus, tetapi penghapusan role produksi sebaiknya dilarang.

## 4. Keputusan yang Harus Disetujui

1. Apakah role wajib selalu satu per user, atau perlu many-to-many user-role.
2. Apakah status, role, dan event memakai enum string tervalidasi atau tabel referensi.
3. Retensi dan penghapusan tiket beserta cascade.
4. Struktur SLA business hours, hari libur, pause, dan escalation.
5. Apakah `ticket_ratings` hanya satu rating akhir atau mendukung banyak rating.
6. Apakah `is_internal` pada pesan diperlukan.
7. Format generator `ticket_number` dan kebutuhan concurrency.

## 5. Hasil Validasi Awal

- Relasi inti sudah mencakup satu organisasi tanpa `organizations` atau `organization_id`.
- Foreign key utama sudah teridentifikasi untuk requester, assignee, actor, master data, pesan, lampiran, histori, notifikasi, dan rating.
- Rekomendasi implementasi: gunakan `restrict/no action` untuk data master dan nonaktifkan user/master data, bukan hard delete.
- `ticket_ratings.ticket_id` unique berarti satu rating akhir per tiket. Ini konsisten dengan kebutuhan awal, tetapi tetap perlu persetujuan.
- `sla_policies` dengan `priority_id` dan `category_id` nullable membutuhkan aturan pemilihan policy jika beberapa record cocok; aturan prioritas pencocokan belum ditentukan.
- Kolom status/event string tervalidasi di service layer direkomendasikan untuk versi awal agar tidak menambah tabel referensi, dengan konstanta terpusat dan validasi transisi.

Migration tetap ditunda sampai keputusan terbuka disetujui.
