# BlablaTrack API Documentation

Base URL development: `http://127.0.0.1:8000/api`

Gunakan header berikut pada endpoint terproteksi:

```http
Accept: application/json
Authorization: Bearer {token}
```

## Authentication

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| POST | `/auth/login` | Tidak | Login dengan `email` dan `password`; mengembalikan token. |
| GET | `/auth/me` | Ya | Profil pengguna aktif. |
| POST | `/auth/logout` | Ya | Menghapus token aktif. |

Login hanya dapat dilakukan oleh akun aktif. Token Sanctum dikirim sekali pada login dan logout menghapus token yang sedang digunakan.

Protected API routes require both `auth:sanctum` and the active-account check. A token belonging to a user deactivated after login is rejected with HTTP 403.

## Tickets

| Method | Endpoint | Akses |
|---|---|---|
| GET | `/tickets` | Pengguna login; hasil sesuai kewenangan. |
| POST | `/tickets` | Pengguna login. |
| GET | `/tickets/{ticket}` | Requester, assignee aktif, Admin, Koordinator. |
| PUT | `/tickets/{ticket}` | Requester dapat mengubah tiket berstatus Open; Admin/Koordinator dapat mengubah metadata sesuai kewenangan. |
| PATCH | `/tickets/{ticket}/status` | Staff terkait atau requester untuk konfirmasi akhir. |

Pembuatan tiket membutuhkan `title`, `description`, `category_id`, dan `priority_id`. Status awal adalah `Open`.

## Assignment, Messages, and Attachments

| Method | Endpoint | Akses |
|---|---|---|
| POST | `/tickets/{ticket}/assignments` | Admin, Koordinator. |
| POST | `/assignments/{assignment}/accept` | Petugas yang ditugaskan. |
| GET/POST | `/tickets/{ticket}/messages` | Requester, assignee, Admin, Koordinator. |
| GET/POST | `/tickets/{ticket}/attachments` | Requester, assignee, Admin, Koordinator. |
| GET | `/attachments/{attachment}/download` | Pengguna yang berhak atas tiket. |

Assignment membutuhkan `assignee_id` Petugas aktif. Pesan membutuhkan `body`; `is_internal=true` hanya untuk Admin/Koordinator. Attachment maksimum 10 MB dengan tipe JPG, PNG, PDF, DOC/DOCX, XLS/XLSX, TXT, atau ZIP.

## Notifications and Dashboard

| Method | Endpoint | Akses |
|---|---|---|
| GET | `/notifications` | Pengguna login, hanya notifikasi sendiri. |
| GET | `/notifications/unread-count` | Pengguna login, jumlah notifikasi belum dibaca sendiri. |
| PATCH | `/notifications/{notification}/read` | Pemilik notifikasi. |
| GET | `/dashboard/summary` | Admin, Koordinator. |

Dashboard summary juga dapat diakses oleh Petugas dan Pelapor; hasilnya otomatis dibatasi pada tiket yang menjadi kewenangan role tersebut.

| GET | `/reports/tickets` | Pengguna login; hasil dibatasi sesuai role. |

Parameter laporan: `from`, `to`, `status`, `priority_id`, `category_id`, `department_id`, `assignee_id`, `group_by`. Nilai `group_by`: `status`, `priority`, `category`, `department`, atau `assignee`.

Catatan: metrik SLA overdue dikembalikan `null` sampai kalender kerja, hari libur, dan aturan pause SLA ditetapkan.

Dashboard menerima parameter opsional `from` dan `to` dalam format `YYYY-MM-DD`.

## Administration

Semua endpoint berikut membutuhkan role Admin:

- `GET|POST /users`, `PUT /users/{user}`
- `GET|POST /departments`, `PUT /departments/{department}`
- `GET|POST /categories`, `PUT /categories/{category}`
- `GET|POST /priorities`, `PUT /priorities/{priority}`

Additional master-data endpoints:

- `GET /users/{user}`, `PATCH /users/{user}/toggle-active`
- `GET /departments/{department}`, `GET /departments/{department}/members`
- `GET /categories/{category}`, `PATCH /categories/{category}/toggle-active`
- `GET /priorities/{priority}`, `PATCH /priorities/{priority}/toggle-active`
- `GET|POST /sla-policies`, `GET /sla-policies/{slaPolicy}`, `PUT /sla-policies/{slaPolicy}`

List endpoints accept `page`; user lists accept `search`, `role_id`, and `department_id`; department/category/priority/SLA lists accept `search`. Category and priority lists also accept `is_active`.

Koordinator dan Admin dapat mengambil daftar petugas aktif untuk penugasan melalui `GET /users/assignees`. Detail tiket memuat relasi assignment dan history beserta pengguna terkait.

## Status Ticket

Transisi yang didukung:

```text
Open -> Assigned | In Progress
Assigned -> In Progress | Pending
In Progress -> Pending | Resolved
Pending -> In Progress
Resolved -> Closed | In Progress
```

Pelapor hanya dapat mengonfirmasi `Resolved -> Closed`.

## Error Response

Validation biasanya menghasilkan HTTP 422, authentication menghasilkan 401, dan authorization menghasilkan 403.

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field": ["The field is required."]
  }
}
```

## API Conventions

- Semua route berada di bawah prefix `/api` dan mengembalikan JSON, termasuk error 401, 403, 404, dan 422.
- List endpoint menggunakan pagination Laravel dengan parameter `page`; ukuran halaman default adalah 20.
- Filter yang tersedia saat ini: dashboard memakai `from` dan `to`; filter tiket lanjutan akan ditambahkan bersama kebutuhan modul terkait.
- Endpoint menggunakan route model binding untuk resource bertipe ID.
- CORS membaca `FRONTEND_URL` dari environment; default development adalah `http://127.0.0.1:5500`.
- Response tidak mengembalikan password, token hash, atau data kredensial sensitif.
