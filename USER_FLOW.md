# BlablaTrack User Flow

## 1. Login

```text
Login → validasi kredensial → token diterima → dashboard sesuai role
                         └→ error → tampilkan pesan tanpa membocorkan detail
```

## 2. Pelapor Membuat Tiket

```text
Dashboard → Buat Tiket → isi judul/deskripsi/kategori/prioritas
→ validasi → tiket Open → tampilkan detail dan nomor tiket
```

## 3. Koordinator Memproses Tiket

```text
Tiket masuk → tinjau detail → atur prioritas → pilih Petugas
→ Assigned → pantau histori dan status
```

## 4. Petugas Menangani Tiket

```text
Tiket Saya → terima assignment → In Progress
→ kirim catatan/tambahan → Pending atau Resolved
```

## 5. Konfirmasi Pelapor

```text
Resolved → pelapor memeriksa hasil → Closed atau kembali In Progress
→ rating penyelesaian
```

## 6. Sitemap Peran

```text
Admin       Dashboard | Tiket | Pengguna | Master Data | Laporan | Activity Log
Koordinator Dashboard | Tiket Masuk | Penugasan | Laporan | Notifikasi
Petugas     Dashboard | Tiket Saya | Notifikasi | Profil
Pelapor     Dashboard | Tiket Saya | Buat Tiket | Notifikasi | Profil
```

## 7. State yang Wajib Dirancang

- Loading: skeleton, tombol disabled selama submit.
- Empty: penjelasan dan CTA yang relevan.
- Error: pesan ramah, field terkait ditandai, opsi retry.
- Success: toast dan navigasi ke resource yang baru dibuat.
- Unauthorized: halaman 403 tanpa menampilkan data yang dilindungi.
