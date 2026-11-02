# BlablaTrack UI/UX Guidelines

## 1. Prinsip Produk

- Utamakan kejelasan status tiket dan tindakan berikutnya.
- Tampilkan hanya menu sesuai role pengguna.
- Gunakan bahasa Indonesia yang singkat dan konsisten.
- Berikan feedback untuk loading, berhasil, kosong, validasi, dan error.
- Jangan gunakan warna sebagai satu-satunya penanda status; sertakan teks dan ikon.

## 2. Struktur Layout

Desktop menggunakan sidebar kiri, topbar, dan area konten utama. Tablet menggunakan sidebar yang dapat diciutkan. Mobile menggunakan topbar dengan menu drawer dan tabel berubah menjadi card list.

Struktur halaman:

```text
App shell
├── Sidebar / navigation
├── Topbar: breadcrumb, search, notification, profile
└── Main content: page title, actions, filters, content, pagination
```

## 3. Pola Interaksi

- Form memakai label permanen, helper text, validasi inline, dan ringkasan error.
- Aksi destruktif atau perubahan status memakai confirmation dialog.
- Status tiket selalu ditampilkan sebagai badge dengan teks.
- Tabel mendukung pagination, empty state, dan skeleton/loading state.
- Detail tiket memakai layout dua kolom: informasi utama dan panel aktivitas/riwayat.

## 4. Role Navigation

- **Admin:** Dashboard, Tiket, Pengguna, Departemen, Kategori, Prioritas, SLA, Laporan, Activity Log.
- **Koordinator:** Dashboard, Tiket Masuk, Penugasan, Laporan, Notifikasi.
- **Petugas:** Dashboard, Tiket Saya, Notifikasi, Profil.
- **Pelapor:** Dashboard, Tiket Saya, Buat Tiket, Notifikasi, Profil.

## 5. Responsive Rules

- Minimum target touch area 44px.
- Filter tabel berubah menjadi panel/modal pada mobile.
- Sidebar tidak memaksa horizontal scroll.
- Form dua kolom menjadi satu kolom pada lebar kecil.
- Tabel kompleks menggunakan card atau horizontal scroll terkontrol.

## 6. Accessibility

- Kontras teks dan status memenuhi WCAG AA.
- Semua input memiliki label dan focus state.
- Modal dapat ditutup dengan Escape.
- Ikon memiliki tooltip atau label yang dapat dibaca screen reader.
- Navigasi keyboard tersedia untuk form, tabel, dan dialog.
