# User Flow — IITC API

Aplikasi ini memiliki **dua jenis aktivitas utama** yang bisa diikuti user:
- 🏆 **Competition** — ikut lomba secara tim atau individu
- 🎓 **Seminar** — daftar seminar gratis dan dapatkan sertifikat otomatis

---

## Alur Lengkap User

```
REGISTER → VERIFY EMAIL → LOGIN → LENGKAPI PROFIL → PILIH JALUR
                                                         │
                              ┌──────────────────────────┴──────────────────────┐
                              ▼                                                   ▼
                        🏆 COMPETITION                                     🎓 SEMINAR
                              │                                                   │
               ┌──────────────┴──────────────┐                           DAFTAR SEMINAR (GRATIS)
               ▼                              ▼                                   │
          BUAT TIM                    IKUT SEBAGAI INDIVIDU              TUNGGU VERIFIKASI KEHADIRAN
               │                              │                                   │
     INVITE ANGGOTA (opsional)         (team otomatis dibuat)              ✅ HADIR → sertifikat PDF otomatis
               │                              │                            ❌ TIDAK HADIR → tidak dapat sertifikat
               └──────────────┬───────────────┘
                              ▼
                       LENGKAPI DATA TIM
                       (nama, avatar, judul)
                              │
                       UPLOAD BUKTI BAYAR
                              │
                       TUNGGU VERIFIKASI ADMIN
                              │
                       ✅ VALID → bisa submit karya
                       ❌ INVALID → upload ulang
                              │
                       SUBMIT KARYA (link)
```

---

## 1. Registrasi & Autentikasi

### 1.1 Register
```
POST /api/register
```
- Input: `fullName`, `email`, `password`, `phone`
- Setelah register, email verifikasi dikirim otomatis
- Response berisi `id`, `hash`, `expires`, `signature` untuk konstruksi URL verifikasi

### 1.2 Verifikasi Email
```
GET /api/verify-email/{id}/{hash}?expires={expires}&signature={signature}
```
- Link dikirim ke email user
- Email **harus diverifikasi** sebelum bisa akses route yang butuh `verified`
- Middleware: `signed`, `throttle:6,1`

### 1.3 Login
```
POST /api/login
```
- Input: `email`, `password`
- Response: `access_token` (Sanctum token), `email_verified_at`
- Token dipakai sebagai `Authorization: Bearer {token}` di semua request selanjutnya

### 1.4 Logout
```
POST /api/logout
```
- Middleware: `auth:sanctum`
- Menghapus token aktif

### 1.5 Lupa Password
```
POST /api/forgot-password    → kirim link reset ke email
POST /api/reset-password     → reset dengan token dari email
```

---

## 2. Profil

### 2.1 Lihat Profil
```
GET /api/profile
```
- Response: data `user` + data `participant` (nama, grade, institusi, avatar, dll)

### 2.2 Update Profil
```
POST /api/profile
```
- Input: `fullName`, `phone`, `grade`, `institution`, `studentId`, `gender`
- File: `avatar`, `photoIdentity`, `twibbon`
- Profil participant dibuat otomatis jika belum ada (upsert)

---

## 3. Melihat Kompetisi (Publik)

Tidak perlu login.

```
GET /api/competitions                    → list semua kompetisi aktif (by event aktif)
GET /api/competitions/{slug}             → detail kompetisi (kriteria, techstack, kategori, dll)
GET /api/competitions/categories         → list semua kategori kompetisi
```

---

## 4. Jalur Competition 🏆

### 4.1 Lihat Kompetisi Saya
```
GET /api/competitions/mine
```
- Menampilkan semua tim yang diikuti (sebagai leader maupun member)
- Response: `teamId`, `competitionName`, `teamName`, `maxMembers`, `currentMembers`, `isActive` (status payment)

---

### 4.2 Opsi A — Buat Tim (Multiplayer)

#### Buat Tim
```
POST /api/teams/{competitionSlug}
```
- Input: `name` (nama tim)
- User otomatis jadi **leader** tim
- Response: `id`, `code`, `name` — **simpan `code`**, dibagikan ke anggota untuk join

#### Anggota Join Tim
```
PUT /api/teams/join
```
- Input: `code` (kode unik tim)
- User yang join jadi **member**
- Leader tidak bisa join timnya sendiri

#### Hapus Anggota dari Tim
```
DELETE /api/teams/{teamId}/members/{memberId}
```
- Hanya leader yang bisa hapus anggota

---

### 4.3 Opsi B — Daftar Individu

```
POST /api/individual/{competitionSlug}
```
- Untuk kompetisi yang boleh `max_members = 1`
- Tim dibuat otomatis di background (tanpa nama/kode)

---

### 4.4 Kelola Tim

#### Lihat Detail Tim
```
GET /api/teams/{teamId}
```
- Response: info tim, leader, daftar member, status payment

#### Update Tim
```
POST /api/teams/mine/update
```
- Input: `name`, `title`
- File: `avatar`

#### Hapus Tim
```
DELETE /api/teams/{teamId}
```
- Hanya leader yang bisa hapus tim

---

### 4.5 Upload Bukti Pembayaran Kompetisi

```
POST /api/payment/{teamId}
```
- File: `proveOfPayment` (foto/bukti transfer)
- Status payment otomatis jadi `PENDING` menunggu verifikasi admin
- Status: `null` (belum bayar) → `PENDING` → `VALID` / `INVALID`

---

### 4.6 Submit Karya

```
POST /api/teams/mine/submission
```
- Input: `submission` (URL link karya)
- Hanya leader tim yang dapat melakukan submit karya

---

## 5. Jalur Seminar 🎓

Seminar **GRATIS** — tidak ada biaya pendaftaran. Sertifikat dihasilkan otomatis oleh sistem.

### 5.1 Daftar Seminar (Gratis)

```
POST /api/seminar/register
```
- Tidak perlu upload bukti bayar
- Response: konfirmasi pendaftaran berhasil
- Jika sudah pernah daftar → error 409 Conflict

### 5.2 Cek Status Pendaftaran Seminar (User)

```
GET /api/seminar/{userId}
```
- Response: nama, email, status kehadiran (`attended`), URL sertifikat jika sudah terbit

### 5.3 [ADMIN] Verifikasi Kehadiran & Generate Sertifikat

```
POST /api/seminar/{userId}/verify-attendance
```
- Input: `isApprove` (boolean)
- Jika `isApprove = true`: admin menandai user hadir → sertifikat PDF otomatis dibuat dan disimpan
- Jika `isApprove = false`: tandai tidak hadir, tidak ada sertifikat

### 5.4 Download Sertifikat

```
GET /api/seminar/{userId}/certificate
```
- Mengembalikan file PDF sertifikat
- 404 jika kehadiran belum diverifikasi admin

---

## Ringkasan Status Seminar

| Status | Artinya |
|---|---|
| `attended: false` | Terdaftar, belum diverifikasi kehadiran |
| `attended: true` | Hadir — sertifikat sudah diterbitkan |

---

## Ringkasan Route User

```
[PUBLIC — tanpa login]
  POST   /api/register
  POST   /api/login
  GET    /api/competitions
  GET    /api/competitions/categories
  GET    /api/competitions/{slug}

[AUTH: sanctum]
  POST   /api/logout

[AUTH: sanctum + email verified]
  ─ Profile ──────────────────────────────────────────────────────
  GET    /api/profile
  POST   /api/profile

  ─ Competitions ─────────────────────────────────────────────────
  GET    /api/competitions/mine              ← lihat kompetisi saya
  
  ─ Teams ────────────────────────────────────────────────────────
  POST   /api/teams/{competitionSlug}                  ← buat tim
  PUT    /api/teams/join                                ← join tim via kode
  GET    /api/teams/{teamId}                            ← detail tim
  POST   /api/teams/{teamId}/update                     ← update tim & submit karya
  DELETE /api/teams/{teamId}                            ← hapus tim
  DELETE /api/teams/{teamId}/members/{memberId}         ← kick member

  ─ Payment Competition ──────────────────────────────────────────
  POST   /api/payment/{teamId}              ← upload bukti bayar kompetisi

  ─ Seminar ──────────────────────────────────────────────────────
  POST   /api/seminar/register                  ← daftar seminar (gratis)
  GET    /api/seminar/{userId}                  ← cek status pendaftaran
  GET    /api/seminar/{userId}/certificate      ← download sertifikat PDF
  POST   /api/seminar/{userId}/verify-attendance ← [ADMIN] verifikasi kehadiran & generate sertifikat
```
