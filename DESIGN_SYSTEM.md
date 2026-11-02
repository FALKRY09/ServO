# BlablaTrack Design System

## 1. Visual Direction

Gaya visual: profesional, tenang, informatif, dan ringan. Gunakan bidang putih, abu-abu kebiruan sebagai surface, serta biru sebagai primary action.

## 2. Design Tokens

### Colors

| Token | Value | Penggunaan |
|---|---|---|
| Primary | `#2563EB` | Tombol utama, link, focus |
| Primary dark | `#1D4ED8` | Hover/active |
| Surface | `#FFFFFF` | Card dan panel |
| Background | `#F8FAFC` | Latar aplikasi |
| Text | `#0F172A` | Teks utama |
| Muted | `#64748B` | Teks sekunder |
| Success | `#16A34A` | Resolved, Closed, berhasil |
| Warning | `#D97706` | Pending, perhatian |
| Danger | `#DC2626` | Error, overdue |
| Info | `#0891B2` | Informasi tambahan |

### Ticket Status

| Status | Warna |
|---|---|
| Open | Info |
| Assigned | Primary |
| In Progress | Primary dark |
| Pending | Warning |
| Resolved | Success |
| Closed | Muted |

### Typography and Spacing

- Font: `Inter`, fallback `system-ui, sans-serif`.
- Heading page: 24px / 32px, semibold.
- Body: 14px / 22px.
- Caption: 12px / 18px.
- Spacing scale: 4, 8, 12, 16, 24, 32, 48px.
- Border radius: 6px input, 8px card, 10px modal.
- Shadow: subtle `0 1px 3px rgba(15, 23, 42, .08)`.

## 3. Components

- Button: primary, secondary, danger, ghost; tinggi minimum 40px.
- Input/select: label, field, helper/error text.
- Badge: status dengan teks dan warna.
- Card: padding 16–24px, surface putih, border tipis.
- Table: header muted, row hover, pagination di bawah.
- Toast: success/error/info, tidak menutup data utama.
- Modal: judul, isi, aksi utama, cancel, dan focus trap.
- Empty state: ikon, penjelasan singkat, CTA bila tersedia.
- Skeleton: placeholder untuk card, tabel, dan detail.
