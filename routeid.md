# Alur Pengguna & Daftar Route API Application

Dokumen ini menguraikan daftar route API yang telah disesuaikan dengan route aktual pada proyek (`routes/api.php`), disusun berdasarkan alur perjalanan pengguna (user journey) serta fungsi manajemen Admin dalam format daftar (list).

---

## 1. Alur Otentikasi & Akun (Authentication Flow)
Langkah pertama bagi pengguna adalah mendaftar akun dan melakukan autentikasi untuk mendapatkan token API (Sanctum).

- `POST` `/api/register` - Pendaftaran akun pengguna baru (Autentikasi: Tidak)
- `POST` `/api/login` - Masuk untuk menerima token Sanctum (Autentikasi: Tidak)
- `POST` `/api/logout` - Keluar dan membatalkan token aktif saat ini (Autentikasi: Ya)

---

## 2. Eksplorasi Publik (Public Discovery)
Pengguna dapat menjelajahi daftar kompetisi dan kategori yang tersedia tanpa perlu login.

- `GET` `/api/competitions` - Mendapatkan daftar seluruh kompetisi aktif (Autentikasi: Tidak)
- `GET` `/api/competitions/categories` - Mendapatkan daftar kategori kompetisi (Autentikasi: Tidak)
- `GET` `/api/competitions/{slug}` - Mendapatkan rincian informasi kompetisi tertentu berdasarkan slug (Autentikasi: Tidak)

---

## 3. Profil Pengguna & Dashboard (User Profile & Dashboard)
Setelah masuk (login), pengguna dapat mengelola profil mereka dan melihat kompetisi/tim yang diikuti.

- `GET` `/api/profile` - Melihat profil pengguna dan data peserta (Autentikasi: Ya)
- `POST` `/api/profile` - Memperbarui profil pengguna dan data peserta (Autentikasi: Ya)
- `GET` `/api/competitions/mine` - Dashboard: Melihat seluruh tim dan kompetisi yang diikuti oleh pengguna (Autentikasi: Ya)

---

## 4. Partisipasi Kompetisi & Manajemen Tim (Team & Individual Management)
Pengguna dapat mendaftar kompetisi secara individu, membuat tim baru, atau bergabung ke tim yang ada menggunakan kode tim.

- `POST` `/api/individual/{competitionSlug}` - Mendaftar kompetisi kategori individu (Autentikasi: Ya)
- `POST` `/api/teams/{competitionSlug}` - Membuat tim baru untuk kompetisi kelompok (Autentikasi: Ya)
- `PUT` `/api/teams/join` - Bergabung ke tim yang ada menggunakan kode tim (Autentikasi: Ya)
- `GET` `/api/teams` - Melihat daftar tim pengguna (Autentikasi: Ya)
- `GET` `/api/teams/{teamId}` - Melihat rincian tim tertentu (hanya ketua atau anggota tim) (Autentikasi: Ya)
- `POST` `/api/teams/{teamId}/update` - Memperbarui nama tim, judul, atau unggah karya (Khusus Ketua Tim) (Autentikasi: Ya)
- `DELETE` `/api/teams/{teamId}/members/{memberId}` - Mengeluarkan anggota dari tim (Khusus Ketua Tim) (Autentikasi: Ya)
- `DELETE` `/api/teams/{teamId}` - Membubarkan/menghapus tim (Khusus Ketua Tim) (Autentikasi: Ya)

---

## 5. Pendaftaran & Sertifikat Seminar (Seminar Registration)
Pengguna dapat mendaftar seminar IITC secara gratis. Sertifikat PDF otomatis dibuat setelah admin memverifikasi kehadiran.

- `GET` `/api/seminar` - Melihat daftar/status seminar (Autentikasi: Ya)
- `POST` `/api/seminar/register` - Mendaftar seminar (gratis, tanpa biaya) (Autentikasi: Ya)
- `GET` `/api/seminar/{userId}` - Memeriksa status pendaftaran seminar pengguna dan URL sertifikat (Autentikasi: Ya)
- `GET` `/api/seminar/{userId}/certificate` - Mengunduh sertifikat PDF pengguna (Autentikasi: Ya)
- `POST` `/api/seminar/{userId}/verify-attendance` - [ADMIN] Memverifikasi kehadiran pengguna dan membuat sertifikat otomatis (Autentikasi: Ya)

---

## 6. Pembayaran Kompetisi (Competition Payments)
Setelah terdaftar dalam tim/kompetisi, pengguna wajib mengunggah bukti pembayaran.

- `POST` `/api/payment/{teamId}` - Mengunggah bukti pembayaran untuk tim kompetisi tertentu (Autentikasi: Ya)
- `POST` `/api/payment/{teamId}/payment-status` - [ADMIN] Memperbarui status verifikasi pembayaran tim (Autentikasi: Ya)