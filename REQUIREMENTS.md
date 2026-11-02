# BlablaTrack — Requirements

## 1. Functional Requirements

### Authentication & Authorization

- Pengguna dapat login dan logout menggunakan kredensial yang valid.
- Password disimpan dalam bentuk hash.
- API menggunakan Laravel Sanctum.
- Endpoint dan data dibatasi berdasarkan role serta kepemilikan/penugasan tiket.
- Status akun aktif/nonaktif memengaruhi akses.

### Administration

- Admin mengelola pengguna, role, permission, departemen, kategori, prioritas, dan kebijakan SLA.
- Perubahan penting dicatat dalam activity log.

### Ticketing

- Pelapor membuat tiket dengan judul, deskripsi, kategori, prioritas, dan lampiran opsional.
- Koordinator melihat tiket masuk, menyesuaikan prioritas, dan menugaskan petugas.
- Petugas melihat tiket yang ditugaskan, mengirim tanggapan, mengubah status, dan menambahkan catatan penyelesaian.
- Pelapor melihat tiket miliknya dan memberi informasi tambahan.
- Pelapor dapat mengonfirmasi penyelesaian dan memberi rating setelah tiket resolved/closed sesuai aturan final.
- Semua perubahan status dan penugasan memiliki riwayat.

### Communication, Files, and Notifications

- Pesan tiket membedakan pesan publik dan catatan internal bila fitur tersebut disetujui.
- Lampiran divalidasi berdasarkan ukuran, MIME type, ekstensi, dan akses pengguna.
- Pengguna menerima notifikasi atas event tiket yang relevan.

### Reporting

- Dashboard menampilkan metrik tiket berdasarkan status, prioritas, kategori, departemen, SLA, dan periode.
- Admin dan koordinator dapat melihat laporan sesuai kewenangan.

## 2. Non-Functional Requirements

- Seluruh input API divalidasi di backend.
- Error API memakai format JSON konsisten.
- Password, token, dan data sensitif tidak dikirim atau dicatat secara tidak aman.
- Foreign key SQLite aktif.
- Query memakai Eloquent dan index pada kolom pencarian/filter utama.
- Zona waktu aplikasi ditetapkan secara eksplisit.
- Struktur kode mengikuti konvensi Laravel dan pemisahan tanggung jawab.
- Fitur diuji melalui automated test/API test sebelum dinyatakan selesai.

## 3. Role Access Matrix (Awal)

| Fitur | Admin | Koordinator | Petugas | Pelapor |
|---|---:|---:|---:|---:|
| Kelola pengguna/master data | Ya | Tidak | Tidak | Tidak |
| Lihat seluruh tiket | Ya | Ya | Tidak | Tidak |
| Buat tiket | Ya* | Ya* | Ya* | Ya |
| Tugaskan petugas | Ya | Ya | Tidak | Tidak |
| Lihat tiket ditugaskan | Ya | Ya | Ya | Tidak |
| Kirim pesan | Ya | Ya | Ya | Ya, tiket sendiri |
| Ubah status | Ya | Ya | Ya, tiket ditugaskan | Terbatas, konfirmasi |
| Rating | Tidak wajib | Tidak wajib | Tidak | Ya, tiket sendiri |
| Laporan | Ya | Sesuai kewenangan | Terbatas | Tidak |

`*` perlu dikonfirmasi apakah admin, koordinator, dan petugas juga boleh membuat tiket sebagai pelapor.

## 4. Validasi dan Aturan Data Awal

- Email pengguna unik; role, departemen, kategori, dan prioritas harus valid serta aktif.
- Tiket wajib memiliki requester, judul, deskripsi, kategori, prioritas, dan status.
- Assignment wajib memiliki tiket dan petugas yang aktif.
- Rating hanya satu per tiket per pelapor dan hanya setelah kondisi penyelesaian terpenuhi.
- Penghapusan data master yang sudah direferensikan sebaiknya memakai nonaktif/soft delete sesuai keputusan final, bukan penghapusan paksa.

## 5. Kebutuhan yang Belum Jelas

1. Nama resmi aplikasi: BlablaTrack atau ServiTrack.
2. Apakah `permissions` harus granular atau role-only sudah cukup untuk versi pertama.
3. Aturan lengkap transisi status dan siapa yang boleh melakukan setiap transisi.
4. Apakah tiket otomatis ditutup atau menunggu konfirmasi pelapor, serta batas waktu auto-close.
5. Definisi SLA: target response/resolution, kalender kerja, hari libur, timezone, pause saat pending, dan eskalasi.
6. Apakah pesan internal/private note diperlukan.
7. Penyimpanan lampiran: disk lokal, ukuran maksimum, tipe file, dan kebijakan retensi.
8. Kanal notifikasi: in-app saja atau email juga.
9. Definisi kewenangan koordinator: seluruh organisasi atau departemen tertentu.
10. Kebijakan deaktivasi pengguna yang memiliki tiket aktif.
