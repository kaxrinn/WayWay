# LAPORAN PENGUJIAN PERANGKAT LUNAK
# (SOFTWARE TESTING REPORT)

---

```
LAPORAN PENGUJIAN PERANGKAT LUNAK
(SOFTWARE TESTING REPORT)

Nama Aplikasi    : WayWay - Platform Pariwisata
Versi            : 1.0.0
Tanggal Pengujian: [21-23 Juni 2026]
Dibuat Oleh      : [Kharina Shinta Jakti Pamungkas]
NIM              : [3312401067]
Program Studi    : [D3 Teknik Informatika]
Institusi        : [Politeknik Negeri Batam]
Dosen Pembimbing : [Dwi Amalia Purnamasari, S.T.,M.Cs]
Dosen Mata Kuliah  : [Muhammad Idris, S.Tr., M.Tr.Kom]
```

---

## DAFTAR ISI

1. [Pendahuluan (Introduction)](#1-pendahuluan)
2. [Deskripsi Aplikasi (Application Description)](#2-deskripsi-aplikasi)
3. [Strategi Pengujian (Testing Strategy)](#3-strategi-pengujian)
4. [Lingkungan Pengujian (Test Environment)](#4-lingkungan-pengujian)
5. [Pengujian Unit (Unit Testing)](#5-pengujian-unit)
6. [Pengujian Integrasi (Integration Testing)](#6-pengujian-integrasi)
7. [Pengujian Sistem (System Testing)](#7-pengujian-sistem)
8. [Pengujian Fungsional API (API Functional Testing)](#8-pengujian-fungsional-api)
9. [Pengujian Keamanan (Security Testing)](#9-pengujian-keamanan)
10. [Pengujian Performa (Performance Testing)](#10-pengujian-performa)
11. [Ringkasan Hasil Pengujian (Test Results Summary)](#11-ringkasan-hasil-pengujian)
12. [Defect Report](#12-defect-report)
13. [Kesimpulan dan Rekomendasi](#13-kesimpulan-dan-rekomendasi)
- [Appendix A: Test Execution Commands](#appendix-a-test-execution-commands)
- [Appendix B: Test File Structure](#appendix-b-test-file-structure)
- [Appendix C: Glossary](#appendix-c-glossary)

---

## 1. PENDAHULUAN

### 1.1 Tujuan Dokumen

Dokumen ini merupakan laporan pengujian perangkat lunak untuk aplikasi WayWay, sebuah platform pariwisata berbasis web yang menghubungkan wisatawan, pemilik destinasi wisata, dan agen perjalanan. Laporan ini mencakup seluruh aktivitas pengujian yang dilakukan untuk memastikan kualitas, keandalan, dan keamanan sistem.

Tujuan spesifik dokumen ini adalah:
- Mendokumentasikan seluruh test cases yang dirancang dan dieksekusi
- Menyajikan hasil pengujian secara terstruktur dan terukur
- Mengidentifikasi defect dan area yang memerlukan perbaikan
- Memberikan rekomendasi untuk peningkatan kualitas sistem
- Memenuhi standar dokumentasi akademik untuk pengujian perangkat lunak

### 1.2 Ruang Lingkup Pengujian

Pengujian mencakup:
- **Pengujian Unit (Unit Testing)** untuk komponen logika bisnis (Services, Models, Validation)
- **Pengujian Integrasi (Integration Testing)** untuk alur kerja antar modul dan interaksi database
- **Pengujian Sistem (System Testing)** untuk skenario end-to-end dari perspektif pengguna
- **Pengujian API** menggunakan Postman Collection untuk seluruh HTTP endpoints
- **Pengujian Keamanan (Security Testing)** berdasarkan OWASP Top 10
- **Pengujian Performa (Performance Testing)** untuk response time dan load handling

### 1.3 Referensi

- Laravel 11 Documentation — https://laravel.com/docs/11.x
- PHPUnit 11 Documentation — https://phpunit.de/documentation.html
- Postman API Testing Documentation — https://learning.postman.com
- IEEE 829 Standard for Software Test Documentation
- OWASP Testing Guide v4.2 — https://owasp.org/www-project-web-security-testing-guide/
- Midtrans Payment Gateway Documentation — https://docs.midtrans.com

---

## 2. DESKRIPSI APLIKASI

### 2.1 Gambaran Umum

WayWay adalah platform pariwisata berbasis web yang dibangun menggunakan framework Laravel 11. Platform ini menghubungkan tiga jenis pengguna utama dalam ekosistem pariwisata:

1. **Wisatawan** — Pengguna yang mencari, menjelajahi, dan merencanakan perjalanan wisata. Wisatawan dapat menggunakan fitur AI Chatbot (Waybot), Itinerary Planner, memberikan ulasan, dan menyimpan destinasi favorit.

2. **Pemilik Destinasi Wisata** — Pengelola destinasi yang mendaftarkan dan mempromosikan tempat wisata mereka melalui sistem paket promosi (Basic, Standard, Premium) dengan integrasi pembayaran Midtrans.

3. **Travel Agent** — Agen perjalanan yang menawarkan paket wisata kepada wisatawan melalui sistem subscription berbasis paket.

4. **Admin** — Administrator platform yang mengelola seluruh entitas, menyetujui konten, dan memantau aktivitas platform.

### 2.2 Arsitektur Sistem

```
┌─────────────────────────────────────────────────────────┐
│                    WayWay Platform                       │
├─────────────────────────────────────────────────────────┤
│  Frontend: Blade Templates + Tailwind CSS + Alpine.js   │
├─────────────────────────────────────────────────────────┤
│  Backend: Laravel 11 (PHP 8.2)                          │
│  ├── Auth: Session-based + Google OAuth (Socialite)     │
│  ├── AI: WaybotService (Chatbot) + ItineraryService     │
│  ├── Payment: Midtrans Snap Integration                 │
│  └── Services: Haversine, Bayesian, OSRM, Greedy Router │
├─────────────────────────────────────────────────────────┤
│  Database: MySQL (Production) / SQLite (Testing)        │
├─────────────────────────────────────────────────────────┤
│  Storage: Laravel Storage (Public Disk)                 │
└─────────────────────────────────────────────────────────┘
```

### 2.3 Modul Utama

| No | Modul | Deskripsi | Role |
|----|-------|-----------|------|
| 1 | Authentication | Login, Register, Google OAuth, Forgot Password | All |
| 2 | Destinasi Management | CRUD destinasi wisata dengan batasan paket | Pemilik |
| 3 | Paket Promosi | Basic/Standard/Premium dengan Midtrans payment | Pemilik |
| 4 | Waybot AI Chatbot | Chatbot berbasis AI untuk rekomendasi wisata | Wisatawan |
| 5 | Itinerary Planner | AI-powered itinerary generation (5-step pipeline) | Wisatawan |
| 6 | Travel Agent | Manajemen paket wisata dengan subscription | Travel Agent |
| 7 | Admin Panel | Manajemen seluruh entitas platform | Admin |
| 8 | Ulasan & Rating | Review dan rating destinasi wisata | Wisatawan |
| 9 | Favorit | Bookmark destinasi favorit | Wisatawan |
| 10 | Edit Request | Sistem permintaan edit untuk paket Basic | Pemilik |

### 2.4 Teknologi Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| Backend Framework | Laravel | 11.x |
| Language | PHP | 8.2+ |
| Database | MySQL | 8.0 |
| Frontend | Blade + Tailwind CSS | - |
| Authentication | Laravel Breeze + Socialite | - |
| Payment | Midtrans Snap | - |
| AI Chatbot | WaybotService (Custom) | - |
| Route Optimization | OSRM + Greedy Router | - |
| Scoring | Bayesian Scoring Service | - |
| Distance | Haversine Formula | - |
| Testing | PHPUnit | 11.x |
| API Testing | Postman | Latest |

### 2.5 Alur Bisnis Utama

**Alur Pemilik Destinasi:**
```
Register → Pilih Paket → Bayar (Midtrans) → Tambah Destinasi → Kelola Konten
```

**Alur Wisatawan:**
```
Register → Jelajahi Destinasi → Gunakan Waybot → Buat Itinerary → Beri Ulasan
```

**Alur Travel Agent:**
```
Register → Subscribe Paket → Tambah Paket Wisata → Kelola Booking
```

---

## 3. STRATEGI PENGUJIAN

### 3.1 Pendekatan Pengujian

Pengujian WayWay menggunakan pendekatan berlapis (layered testing approach) yang mengikuti piramida pengujian:

```
           ┌─────────────────────┐
           │   System Testing    │  ← End-to-end user workflows
           ├─────────────────────┤
           │ Integration Testing │  ← Module interaction testing
           ├─────────────────────┤
           │    Unit Testing     │  ← Individual component testing
           └─────────────────────┘
```

Pendekatan ini memastikan bahwa:
- Komponen individual diuji secara terisolasi (Unit Testing)
- Interaksi antar komponen diverifikasi (Integration Testing)
- Alur kerja pengguna secara keseluruhan divalidasi (System Testing)
- Seluruh endpoint API diuji secara fungsional (API Testing)
- Aspek keamanan dan performa diverifikasi secara khusus

### 3.2 Jenis Pengujian

| Jenis Pengujian | Tools | Cakupan |
|----------------|-------|---------|
| Unit Testing | PHPUnit 11 | Services, Models, Validation |
| Integration Testing | PHPUnit + Postman | Controller flows, DB interactions |
| API Testing | Postman Collection | All HTTP endpoints |
| System Testing | Manual + Postman Runner | End-to-end workflows |
| Security Testing | Manual + OWASP Checklist | Auth, CSRF, XSS, SQL Injection |
| Performance Testing | Postman + Manual | Response times, load |

### 3.3 Kriteria Keberhasilan (Pass/Fail Criteria)

**Pass Criteria:**
- Unit tests: ≥ 90% pass rate
- Integration tests: ≥ 85% pass rate
- System tests: ≥ 80% pass rate
- API tests: ≥ 90% pass rate
- Tidak ada critical security vulnerability yang belum ditangani
- Response time < 3 detik untuk halaman standar
- Response time < 30 detik untuk konten yang di-generate AI
- Code coverage ≥ 70% untuk komponen kritis

**Fail Criteria:**
- Adanya authentication bypass vulnerability
- Data loss atau data corruption
- Payment processing error yang tidak tertangani
- Fitur kritis tidak dapat diakses
- SQL Injection atau XSS vulnerability yang tidak tertangani

### 3.4 Manajemen Risiko Pengujian

| Risiko | Dampak | Mitigasi |
|--------|--------|----------|
| AI service tidak tersedia saat testing | Tinggi | Mock AI responses untuk unit/integration tests |
| Midtrans sandbox tidak responsif | Tinggi | Gunakan webhook simulation |
| Data test terkontaminasi | Sedang | Gunakan database in-memory (SQLite) untuk testing |
| Test environment berbeda dengan production | Sedang | Dokumentasikan perbedaan konfigurasi |

---

## 4. LINGKUNGAN PENGUJIAN

### 4.1 Spesifikasi Hardware

| Komponen | Spesifikasi Minimum | Spesifikasi Rekomendasi |
|----------|--------------------|-----------------------|
| Processor | Intel Core i5 Gen 8 | Intel Core i7 Gen 10+ |
| RAM | 8 GB | 16 GB |
| Storage | 20 GB free space | 50 GB SSD |
| OS | Windows 10 / Ubuntu 20.04 | Windows 11 / Ubuntu 22.04 |
| Network | 10 Mbps | 50 Mbps+ |

### 4.2 Spesifikasi Software

| Software | Versi | Keterangan |
|----------|-------|------------|
| PHP | 8.2+ | Backend runtime |
| Laravel | 11.x | Framework |
| MySQL | 8.0 | Database produksi |
| SQLite | 3.x | Database testing (in-memory) |
| Composer | 2.x | PHP dependency manager |
| Node.js | 18+ | Frontend build tools |
| NPM | 9+ | Package manager |
| Postman | Latest | API testing |
| Newman | Latest | Postman CLI runner |
| Chrome | Latest | Browser testing |

### 4.3 Konfigurasi Testing Environment

```env
# .env.testing
APP_NAME=WayWay
APP_ENV=testing
APP_KEY=base64:[generated-key]
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_DRIVER=sync
MAIL_DRIVER=array

# Midtrans Sandbox
MIDTRANS_SERVER_KEY=SB-Mid-server-[sandbox-key]
MIDTRANS_CLIENT_KEY=SB-Mid-client-[sandbox-key]
MIDTRANS_IS_PRODUCTION=false

# AI Services (Mock untuk testing)
WAYBOT_API_KEY=test-key
WAYBOT_MOCK_MODE=true
```

### 4.4 Test Data

| Data | Nilai |
|------|-------|
| Admin Email | admin@wayway.com |
| Wisatawan Email | wisatawan@test.com |
| Pemilik Email | pemilik@test.com |
| Travel Agent Email | agent@test.com |
| Default Password | Password123! |
| Test Coordinates | Lat: 1.1296758, Lng: 104.0452254 (Batam) |
| Midtrans Mode | Sandbox |
| Test Card Number | 4811 1111 1111 1114 (Midtrans Sandbox) |
| Test Card CVV | 123 |
| Test Card Expiry | 01/25 |

### 4.5 Setup Prosedur

```bash
# 1. Clone repository
git clone [repository-url]
cd wayway

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env.testing
php artisan key:generate --env=testing

# 4. Run migrations (testing uses in-memory SQLite)
php artisan migrate --env=testing

# 5. Seed test data
php artisan db:seed --env=testing

# 6. Build frontend assets
npm run build
```

---

## 5. PENGUJIAN UNIT

### 5.1 Ringkasan Unit Testing

| Test File | Jumlah Test | Pass | Fail | Coverage |
|-----------|-------------|------|------|----------|
| HaversineServiceTest | 6 | - | - | - |
| BayesianScoringServiceTest | 5 | - | - | - |
| ItineraryServiceTest | 5 | - | - | - |
| PaketPromosiTest | 5 | - | - | - |
| UserRolePermissionsTest | 5 | - | - | - |
| ValidationRulesTest | 7 | - | - | - |
| **TOTAL** | **33** | **-** | **-** | **-** |

### 5.2 Detail Test Cases — HaversineService

**Deskripsi:** HaversineService bertanggung jawab menghitung jarak geografis antara dua titik koordinat menggunakan formula Haversine. Service ini digunakan untuk menghitung jarak destinasi dari lokasi pengguna dan untuk optimasi rute itinerary.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-001 | Kalkulasi jarak dua titik diketahui | Batam Center (1.1296758, 104.0452254) → Pantai Nongsa (1.15, 104.12) | 5 km < distance < 20 km | - | - |
| TC-UNIT-002 | Jarak titik yang sama = 0 | lat1=lng1=lat2=lng2 | 0 km | - | - |
| TC-UNIT-003 | Simetri jarak (A→B = B→A) | Point A dan Point B | distance(A,B) == distance(B,A) | - | - |
| TC-UNIT-004 | Jarak lintas kota | Batam → Jakarta | 900 km < distance < 1100 km | - | - |
| TC-UNIT-005 | Input koordinat negatif (belahan selatan) | lat=-6.2, lng=106.8 (Jakarta) | Valid positive distance | - | - |
| TC-UNIT-006 | Sorting destinasi berdasarkan jarak | Array 5 destinasi dengan koordinat berbeda | Array terurut ascending by distance | - | - |

### 5.3 Detail Test Cases — BayesianScoringService

**Deskripsi:** BayesianScoringService menghitung skor destinasi menggunakan metode Bayesian averaging untuk menghasilkan ranking yang adil antara destinasi dengan banyak ulasan dan sedikit ulasan.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-007 | Destinasi dengan lebih banyak ulasan mendapat skor lebih tinggi | dest1: rating=4.5, count=100 vs dest2: rating=4.5, count=5 | score(dest1) > score(dest2) | - | - |
| TC-UNIT-008 | Destinasi Premium mendapat priority boost | regular: priority=1 vs premium: priority=3 (same rating) | score(premium) > score(regular) | - | - |
| TC-UNIT-009 | Destinasi tanpa ulasan mendapat skor global mean | count=0 | score = global_mean | - | - |
| TC-UNIT-010 | Skor dalam rentang valid (0-5) | Berbagai kombinasi rating dan count | 0 ≤ score ≤ 5 | - | - |
| TC-UNIT-011 | Sorting destinasi berdasarkan Bayesian score | Array 10 destinasi | Array terurut descending by score | - | - |

### 5.4 Detail Test Cases — ItineraryService

**Deskripsi:** ItineraryService mengelola pipeline 5-langkah untuk menghasilkan itinerary perjalanan berbasis AI, termasuk pemilihan destinasi, optimasi rute, dan estimasi waktu.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-012 | Pipeline menghasilkan struktur itinerary valid | User preferences, tanggal, durasi | Object dengan days[], total_distance, estimated_cost | - | - |
| TC-UNIT-013 | Greedy router mengoptimalkan urutan kunjungan | 5 destinasi dengan koordinat | Urutan yang meminimalkan total jarak | - | - |
| TC-UNIT-014 | Estimasi waktu kunjungan per destinasi | Tipe destinasi (pantai, museum, dll) | Durasi dalam menit yang realistis | - | - |
| TC-UNIT-015 | Pembagian destinasi per hari sesuai durasi | 10 destinasi, 3 hari | Maks 4 destinasi per hari | - | - |
| TC-UNIT-016 | Fallback ketika OSRM tidak tersedia | OSRM timeout | Gunakan Haversine distance | - | - |

### 5.5 Detail Test Cases — PaketPromosi

**Deskripsi:** Pengujian logika bisnis paket promosi (Basic, Standard, Premium) termasuk batasan jumlah destinasi dan hak akses fitur.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-017 | Paket Basic: max 1 destinasi | User Basic, tambah destinasi ke-2 | Error: limit exceeded | - | - |
| TC-UNIT-018 | Paket Standard: max 3 destinasi | User Standard, tambah destinasi ke-4 | Error: limit exceeded | - | - |
| TC-UNIT-019 | Paket Premium: max 10 destinasi | User Premium, tambah destinasi ke-11 | Error: limit exceeded | - | - |
| TC-UNIT-020 | Paket Basic tidak bisa edit langsung | User Basic, edit destinasi | Redirect ke edit-request | - | - |
| TC-UNIT-021 | Upgrade paket meningkatkan limit | Basic → Standard | max_destinasi berubah dari 1 ke 3 | - | - |

### 5.6 Detail Test Cases — UserRolePermissions

**Deskripsi:** Pengujian sistem role-based access control untuk memastikan setiap role hanya dapat mengakses fitur yang diizinkan.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-022 | Wisatawan tidak bisa akses admin routes | User role=wisatawan | 403 Forbidden | - | - |
| TC-UNIT-023 | Pemilik tidak bisa akses wisatawan-only routes | User role=pemilik | 403 Forbidden | - | - |
| TC-UNIT-024 | Admin bisa akses semua routes | User role=admin | 200 OK | - | - |
| TC-UNIT-025 | Guest tidak bisa akses authenticated routes | Unauthenticated request | 302 → /login | - | - |
| TC-UNIT-026 | Travel agent tidak bisa akses pemilik routes | User role=travel_agent | 403 Forbidden | - | - |

### 5.7 Detail Test Cases — ValidationRules

**Deskripsi:** Pengujian aturan validasi input untuk memastikan data yang masuk ke sistem selalu valid dan aman.

| ID | Test Case | Input | Expected | Actual | Status |
|----|-----------|-------|----------|--------|--------|
| TC-UNIT-027 | Rating ulasan harus 1-5 | rating=0, 6, -1 (invalid); rating=1,2,3,4,5 (valid) | Invalid: fails; Valid: passes | - | - |
| TC-UNIT-028 | Harga tidak boleh negatif | harga=-1000 | Validation fails | - | - |
| TC-UNIT-029 | Email harus format valid | email="bukan-email" | Validation fails | - | - |
| TC-UNIT-030 | Password minimal 8 karakter | password="abc" | Validation fails | - | - |
| TC-UNIT-031 | Koordinat latitude dalam range -90 to 90 | lat=91, lat=-91 | Validation fails | - | - |
| TC-UNIT-032 | Koordinat longitude dalam range -180 to 180 | lng=181, lng=-181 | Validation fails | - | - |
| TC-UNIT-033 | Nama destinasi tidak boleh kosong | nama="" | Validation fails | - | - |

### 5.8 Cara Menjalankan Unit Tests

```bash
# Jalankan semua unit tests
php artisan test --testsuite=Unit

# Jalankan dengan coverage report
php artisan test --coverage --min=70

# Jalankan test spesifik
php artisan test tests/Unit/HaversineServiceTest.php
php artisan test tests/Unit/BayesianScoringServiceTest.php
php artisan test tests/Unit/ItineraryServiceTest.php
php artisan test tests/Unit/PaketPromosiTest.php
php artisan test tests/Unit/UserRolePermissionsTest.php
php artisan test tests/Unit/ValidationRulesTest.php

# Output verbose
php artisan test --verbose tests/Unit/

# Generate HTML coverage report
php artisan test --coverage-html tests/reports/coverage/
```

---

## 6. PENGUJIAN INTEGRASI

### 6.1 Ringkasan Integration Testing

| Skenario | Jumlah TC | Pass | Fail | Notes |
|----------|-----------|------|------|-------|
| User Registration | 5 | - | - | |
| User Login | 7 | - | - | |
| Profile Management | 5 | - | - | |
| Destination Management | 6 | - | - | |
| Travel Agent Management | 5 | - | - | |
| Search & Discovery | 6 | - | - | |
| Package Purchase | 5 | - | - | |
| Package Activation | 3 | - | - | |
| Itinerary Management | 7 | - | - | |
| AI Chatbot (Waybot) | 7 | - | - | |
| Review & Rating | 6 | - | - | |
| **TOTAL** | **62** | **-** | **-** | |

### 6.2 Detail Test Cases — Authentication Integration

**Deskripsi:** Pengujian alur autentikasi lengkap termasuk registrasi, login, logout, dan integrasi dengan database.

| ID | Test Case | Precondition | Steps | Expected | Actual | Status |
|----|-----------|--------------|-------|----------|--------|--------|
| TC-INT-001 | Register wisatawan valid | App running, DB clean | POST /register dengan data valid | 302 → /wisatawan/beranda | - | - |
| TC-INT-002 | Register duplicate email | User dengan email sama sudah ada | POST /register dengan email yang sama | 422 validation error | - | - |
| TC-INT-003 | Register password mismatch | - | POST /register dengan password berbeda | 422 password confirmation error | - | - |
| TC-INT-004 | Register pemilik valid | - | POST /register dengan role=pemilik | 302 → /pemilik/dashboard | - | - |
| TC-INT-005 | Register travel agent valid | - | POST /register dengan role=travel_agent | 302 → /travel-agent/dashboard | - | - |
| TC-INT-006 | Login wisatawan | User wisatawan ada di DB | POST /login dengan kredensial valid | 302 → /wisatawan/beranda | - | - |
| TC-INT-007 | Login admin | User admin ada di DB | POST /login dengan kredensial admin | 302 → /admin/dashboard | - | - |
| TC-INT-008 | Login pemilik | User pemilik ada di DB | POST /login dengan kredensial pemilik | 302 → /pemilik/dashboard | - | - |
| TC-INT-009 | Login travel agent | User travel agent ada di DB | POST /login dengan kredensial agent | 302 → /travel-agent/dashboard | - | - |
| TC-INT-010 | Login wrong password | User ada di DB | POST /login dengan password salah | Redirect dengan session error | - | - |
| TC-INT-011 | Login non-existent user | - | POST /login dengan email tidak ada | Redirect dengan session error | - | - |
| TC-INT-012 | Logout | User authenticated | POST /logout | 302 → / (homepage) | - | - |

### 6.3 Detail Test Cases — Destination Management Integration

**Deskripsi:** Pengujian CRUD destinasi wisata dengan mempertimbangkan batasan paket dan aturan bisnis.

| ID | Test Case | Precondition | Expected | Actual | Status |
|----|-----------|--------------|----------|--------|--------|
| TC-INT-018 | Create destinasi (paket Basic) | User pemilik dengan paket Basic, belum punya destinasi | Success, destinasi tersimpan di DB | - | - |
| TC-INT-019 | Exceed destination limit (Basic) | User pemilik Basic sudah punya 1 destinasi | Error: limit reached, destinasi tidak tersimpan | - | - |
| TC-INT-020 | Edit destinasi Basic (tidak bisa langsung) | User pemilik Basic | Redirect ke halaman edit-request | - | - |
| TC-INT-021 | Edit destinasi Standard (bisa langsung) | User pemilik Standard | Form edit berhasil dimuat dan disimpan | - | - |
| TC-INT-022 | Delete destinasi (soft delete) | Destinasi aktif ada di DB | status=inactive di DB, tidak muncul di listing | - | - |
| TC-INT-023 | Edit destinasi milik user lain | User A mencoba edit destinasi User B | 404 Not Found | - | - |

### 6.4 Detail Test Cases — Package Purchase Integration

**Deskripsi:** Pengujian alur pembelian paket promosi dari inisiasi transaksi hingga aktivasi paket.

| ID | Test Case | Precondition | Expected | Actual | Status |
|----|-----------|--------------|----------|--------|--------|
| TC-INT-030 | Inisiasi pembelian paket Standard | User pemilik login | TransaksiPromosi dibuat dengan status=pending | - | - |
| TC-INT-031 | Midtrans Snap token diterima | Transaksi pending ada | snap_token valid diterima dari Midtrans | - | - |
| TC-INT-032 | Webhook payment success | Transaksi pending ada | status=paid, paket diaktifkan | - | - |
| TC-INT-033 | Webhook invalid signature | - | 403 Forbidden, transaksi tidak diupdate | - | - |
| TC-INT-034 | Webhook payment failed | Transaksi pending ada | status=failed, paket tidak diaktifkan | - | - |

### 6.5 Detail Test Cases — Waybot Integration

**Deskripsi:** Pengujian integrasi Waybot AI Chatbot termasuk manajemen sesi percakapan dan penyimpanan riwayat.

| ID | Test Case | Precondition | Expected | Actual | Status |
|----|-----------|--------------|----------|--------|--------|
| TC-INT-050 | First Waybot message | User wisatawan login | 200 OK, session_token baru dikembalikan | - | - |
| TC-INT-051 | Continue conversation | Session token valid ada | 200 OK, session_token sama dikembalikan | - | - |
| TC-INT-052 | Waybot response berisi rekomendasi | Pesan tentang destinasi wisata | Response mengandung nama destinasi | - | - |
| TC-INT-053 | Waybot response dalam Bahasa Indonesia | Pesan dalam Bahasa Indonesia | Response dalam Bahasa Indonesia | - | - |
| TC-INT-054 | History retrieval | Session dengan beberapa pesan | 200 OK, messages array dikembalikan | - | - |
| TC-INT-055 | Reset session | Session aktif ada | 200 OK, success=true, history terhapus | - | - |
| TC-INT-056 | Empty message validation | - | 422 validation error | - | - |

### 6.6 Detail Test Cases — Review & Rating Integration

**Deskripsi:** Pengujian sistem ulasan dan rating destinasi wisata.

| ID | Test Case | Precondition | Expected | Actual | Status |
|----|-----------|--------------|----------|--------|--------|
| TC-INT-057 | Submit ulasan valid | User wisatawan login, destinasi ada | Ulasan tersimpan, rating destinasi terupdate | - | - |
| TC-INT-058 | Submit ulasan duplikat | User sudah pernah review destinasi ini | Error: sudah pernah memberikan ulasan | - | - |
| TC-INT-059 | Rating harus 1-5 | - | 422 jika rating di luar range | - | - |
| TC-INT-060 | Edit ulasan sendiri | User adalah pemilik ulasan | Ulasan berhasil diupdate | - | - |
| TC-INT-061 | Delete ulasan sendiri | User adalah pemilik ulasan | Ulasan terhapus dari DB | - | - |
| TC-INT-062 | Delete ulasan orang lain | User bukan pemilik ulasan | 403 Forbidden | - | - |

### 6.7 Cara Menjalankan Integration Tests

```bash
# Jalankan semua feature tests
php artisan test --testsuite=Feature

# Jalankan test spesifik per modul
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/DestinasiTest.php
php artisan test tests/Feature/WaybotTest.php
php artisan test tests/Feature/ItineraryTest.php
php artisan test tests/Feature/TravelAgentTest.php
php artisan test tests/Feature/UlasanTest.php
php artisan test tests/Feature/AdminTest.php

# Jalankan semua tests (Unit + Feature)
php artisan test

# Jalankan dengan filter nama test
php artisan test --filter="test_register_wisatawan_valid"

# Jalankan parallel (lebih cepat)
php artisan test --parallel
```

---

## 7. PENGUJIAN SISTEM

### 7.1 Ringkasan System Testing

| Workflow | ID | Jumlah Steps | Pass | Fail | Notes |
|----------|----|-------------|------|------|-------|
| Tourist Complete Journey | SYS-001 | 16 | - | - | |
| Tourist + Waybot Journey | SYS-002 | 10 | - | - | |
| Pemilik Complete Journey | SYS-003 | 20 | - | - | |
| Edit Request Flow | SYS-004 | 10 | - | - | |
| Travel Agent Journey | SYS-005 | 18 | - | - | |
| Payment Complete Flow | SYS-006 | 14 | - | - | |
| Waybot Full Conversation | SYS-007 | 15 | - | - | |
| Itinerary Complete Flow | SYS-008 | 20 | - | - | |
| Admin Management Flow | SYS-009 | 17 | - | - | |
| **TOTAL** | | **140** | **-** | **-** | |

### 7.2 Detail — Tourist Complete Journey (SYS-001)

**Deskripsi:** Skenario lengkap perjalanan wisatawan dari registrasi hingga memberikan ulasan.

**Precondition:** Aplikasi berjalan, database berisi data destinasi wisata.

| Step | Action | Expected | Actual | Pass/Fail |
|------|--------|----------|--------|-----------|
| 1 | Navigate ke homepage (/) | Homepage loads dengan daftar destinasi | - | - |
| 2 | Klik tombol "Daftar" | Halaman registrasi ditampilkan | - | - |
| 3 | Isi form registrasi dengan data valid | Form menerima input | - | - |
| 4 | Submit form registrasi | Redirect ke /wisatawan/beranda | - | - |
| 5 | Lihat halaman beranda wisatawan | Daftar destinasi rekomendasi ditampilkan | - | - |
| 6 | Ketik "pantai" di search bar | Hasil pencarian destinasi pantai muncul | - | - |
| 7 | Klik kartu destinasi | Halaman detail destinasi dimuat | - | - |
| 8 | Klik tombol "Tambah Favorit" | Notifikasi sukses, ikon favorit berubah | - | - |
| 9 | Navigasi ke halaman Favorit | Destinasi yang ditambahkan muncul | - | - |
| 10 | Kembali ke detail destinasi | Halaman detail dimuat | - | - |
| 11 | Scroll ke bagian Ulasan | Form ulasan ditampilkan | - | - |
| 12 | Isi form ulasan (rating=5, komentar) | Form menerima input | - | - |
| 13 | Submit ulasan | Ulasan muncul di halaman, rating terupdate | - | - |
| 14 | Navigasi ke Profil | Halaman profil dimuat | - | - |
| 15 | Edit informasi profil | Perubahan tersimpan | - | - |
| 16 | Logout | Redirect ke homepage, session terhapus | - | - |

### 7.3 Detail — Pemilik Complete Journey (SYS-003)

**Deskripsi:** Skenario lengkap pemilik destinasi dari registrasi, pembelian paket, hingga pengelolaan destinasi.

**Precondition:** Aplikasi berjalan, Midtrans sandbox aktif.

| Step | Action | Expected | Actual | Pass/Fail |
|------|--------|----------|--------|-----------|
| 1 | Register sebagai pemilik | Redirect ke /pemilik/dashboard | - | - |
| 2 | Lihat dashboard pemilik | Info paket Basic ditampilkan | - | - |
| 3 | Klik "Tambah Destinasi" | Form tambah destinasi muncul | - | - |
| 4 | Isi form destinasi pertama | Form menerima input | - | - |
| 5 | Upload foto destinasi | Foto berhasil diupload | - | - |
| 6 | Submit form destinasi | Destinasi tersimpan, muncul di daftar | - | - |
| 7 | Coba tambah destinasi kedua (Basic) | Error: limit paket Basic | - | - |
| 8 | Navigasi ke halaman Paket | Daftar paket Basic/Standard/Premium | - | - |
| 9 | Klik "Upgrade ke Standard" | Halaman checkout muncul | - | - |
| 10 | Klik "Bayar Sekarang" | Midtrans Snap popup muncul | - | - |
| 11 | Isi data kartu kredit sandbox | Form Midtrans menerima input | - | - |
| 12 | Konfirmasi pembayaran | Pembayaran diproses | - | - |
| 13 | Verifikasi webhook diterima | Status transaksi = paid | - | - |
| 14 | Verifikasi paket terupgrade | current_paket = Standard | - | - |
| 15 | Tambah destinasi kedua | Berhasil, destinasi tersimpan | - | - |
| 16 | Edit destinasi pertama | Form edit muncul (Standard bisa langsung edit) | - | - |
| 17 | Simpan perubahan | Perubahan tersimpan | - | - |
| 18 | Lihat statistik destinasi | Data views/ulasan ditampilkan | - | - |
| 19 | Nonaktifkan destinasi | Status berubah ke inactive | - | - |
| 20 | Logout | Session terhapus | - | - |

### 7.4 Detail — Payment Complete Flow (SYS-006)

**Deskripsi:** Skenario lengkap alur pembayaran paket promosi menggunakan Midtrans.

| Step | Action | Expected | Actual | Pass/Fail |
|------|--------|----------|--------|-----------|
| 1 | Login sebagai pemilik | Dashboard pemilik dimuat | - | - |
| 2 | Navigasi ke halaman Paket | 3 pilihan paket ditampilkan | - | - |
| 3 | Klik "Beli" pada paket Standard | Halaman konfirmasi pembelian | - | - |
| 4 | Verifikasi detail paket | Nama, harga, fitur ditampilkan dengan benar | - | - |
| 5 | Klik "Lanjutkan Pembayaran" | POST ke /pemilik/paket/beli | - | - |
| 6 | Verifikasi TransaksiPromosi dibuat | Record di DB dengan status=pending | - | - |
| 7 | Verifikasi snap_token diterima | Token valid dari Midtrans | - | - |
| 8 | Midtrans Snap popup muncul | UI Midtrans ditampilkan | - | - |
| 9 | Pilih metode pembayaran kartu kredit | Form kartu kredit muncul | - | - |
| 10 | Isi data kartu sandbox | Data diterima | - | - |
| 11 | Submit pembayaran | Midtrans memproses | - | - |
| 12 | Webhook notification diterima | POST /api/midtrans/notification | - | - |
| 13 | Verifikasi signature webhook | Signature valid, 200 OK | - | - |
| 14 | Verifikasi status transaksi = paid | DB terupdate | - | - |
| 15 | Verifikasi paket aktif | current_paket = Standard | - | - |
| 16 | Verifikasi limit destinasi | max_destinasi = 3 | - | - |

### 7.5 Detail — Itinerary Complete Flow (SYS-008)

**Deskripsi:** Skenario lengkap pembuatan itinerary perjalanan menggunakan AI.

| Step | Action | Expected | Actual | Pass/Fail |
|------|--------|----------|--------|-----------|
| 1 | Login sebagai wisatawan | Beranda wisatawan dimuat | - | - |
| 2 | Navigasi ke Itinerary Planner | Halaman planner dimuat | - | - |
| 3 | Isi form: kota tujuan = "Batam" | Input diterima | - | - |
| 4 | Isi form: durasi = 3 hari | Input diterima | - | - |
| 5 | Isi form: preferensi = "pantai, kuliner" | Input diterima | - | - |
| 6 | Isi form: budget = "menengah" | Input diterima | - | - |
| 7 | Submit form | Loading indicator muncul | - | - |
| 8 | Tunggu AI processing (max 30 detik) | Progress indicator aktif | - | - |
| 9 | Itinerary berhasil di-generate | Halaman itinerary ditampilkan | - | - |
| 10 | Verifikasi struktur itinerary | 3 hari dengan destinasi per hari | - | - |
| 11 | Verifikasi estimasi waktu | Setiap destinasi punya durasi kunjungan | - | - |
| 12 | Verifikasi estimasi biaya | Total biaya ditampilkan | - | - |
| 13 | Klik "Simpan Itinerary" | Itinerary tersimpan di akun | - | - |
| 14 | Navigasi ke daftar itinerary | Itinerary yang disimpan muncul | - | - |
| 15 | Buka itinerary tersimpan | Detail itinerary ditampilkan | - | - |
| 16 | Klik "Download PDF" | PDF berhasil didownload | - | - |
| 17 | Verifikasi konten PDF | Semua informasi itinerary ada di PDF | - | - |
| 18 | Edit itinerary | Form edit muncul | - | - |
| 19 | Simpan perubahan | Perubahan tersimpan | - | - |
| 20 | Hapus itinerary | Itinerary terhapus dari daftar | - | - |

---

## 8. PENGUJIAN FUNGSIONAL API

### 8.1 Ringkasan API Testing (Postman)

| Folder | Jumlah Request | Pass | Fail | Notes |
|--------|---------------|------|------|-------|
| 🔐 Authentication | 9 | - | - | |
| 👤 Wisatawan | 9 | - | - | |
| 🏛️ Destinasi Public | 6 | - | - | |
| 🤖 Waybot AI | 5 | - | - | |
| 🗺️ Itinerary | 6 | - | - | |
| 🏢 Pemilik Wisata | 12 | - | - | |
| 🧳 Travel Agent | 10 | - | - | |
| 👑 Admin | 19 | - | - | |
| 🔔 Webhooks | 2 | - | - | |
| **TOTAL** | **78** | **-** | **-** | |

### 8.2 Detail API Test Cases

#### 8.2.1 Authentication Endpoints

| Endpoint | Method | Skenario | Expected Status | Expected Response | Actual | Pass/Fail |
|----------|--------|----------|----------------|-------------------|--------|-----------|
| /register | POST | Data valid (wisatawan) | 302 | Redirect ke /wisatawan/beranda | - | - |
| /register | POST | Email duplikat | 422 | JSON errors.email | - | - |
| /register | POST | Password tidak cocok | 422 | JSON errors.password | - | - |
| /login | POST | Kredensial valid | 302 | Redirect sesuai role | - | - |
| /login | POST | Password salah | 302 | Session error message | - | - |
| /login | POST | Email tidak terdaftar | 302 | Session error message | - | - |
| /logout | POST | User authenticated | 302 | Redirect ke / | - | - |
| /forgot-password | POST | Email valid | 302 | Session success message | - | - |
| /auth/google | GET | - | 302 | Redirect ke Google OAuth | - | - |

#### 8.2.2 Waybot AI Endpoints

| Endpoint | Method | Skenario | Expected Status | Expected Response | Actual | Pass/Fail |
|----------|--------|----------|----------------|-------------------|--------|-----------|
| /waybot/chat | POST | Pesan valid, sesi baru | 200 | `{session_token, message, response}` | - | - |
| /waybot/chat | POST | Pesan valid, lanjut sesi | 200 | `{session_token}` sama dengan sebelumnya | - | - |
| /waybot/chat | POST | Pesan kosong | 422 | `{errors: {message: [...]}}` | - | - |
| /waybot/history | GET | Session token valid | 200 | `{messages: [...]}` | - | - |
| /waybot/reset | POST | Session token valid | 200 | `{success: true}` | - | - |

#### 8.2.3 Public API Endpoints

| Endpoint | Method | Skenario | Expected Status | Expected Response | Actual | Pass/Fail |
|----------|--------|----------|----------------|-------------------|--------|-----------|
| /api/destinasi/kategori/{id} | GET | Kategori valid | 200 | JSON array destinasi | - | - |
| /api/destinasi/kategori/99999 | GET | Kategori tidak ada | 200 | Empty array atau 404 | - | - |
| /api/midtrans/notification | POST | Signature valid, payment success | 200 | `{status: "ok"}` | - | - |
| /api/midtrans/notification | POST | Signature tidak valid | 403 | `{error: "Invalid signature"}` | - | - |
| /api/midtrans/notification | POST | Payment failed | 200 | `{status: "ok"}` (status diupdate ke failed) | - | - |

#### 8.2.4 Pemilik Wisata Endpoints

| Endpoint | Method | Skenario | Expected Status | Expected Response | Actual | Pass/Fail |
|----------|--------|----------|----------------|-------------------|--------|-----------|
| /pemilik/dashboard | GET | Authenticated pemilik | 200 | Dashboard HTML | - | - |
| /pemilik/destinasi | GET | Authenticated pemilik | 200 | Daftar destinasi | - | - |
| /pemilik/destinasi/create | GET | Authenticated pemilik | 200 | Form create | - | - |
| /pemilik/destinasi | POST | Data valid, dalam limit | 302 | Redirect ke daftar | - | - |
| /pemilik/destinasi | POST | Melebihi limit paket | 302 | Redirect dengan error | - | - |
| /pemilik/destinasi/{id}/edit | GET | Pemilik destinasi, paket Standard+ | 200 | Form edit | - | - |
| /pemilik/destinasi/{id} | PUT | Data valid | 302 | Redirect ke daftar | - | - |
| /pemilik/destinasi/{id} | DELETE | Pemilik destinasi | 302 | Redirect, status=inactive | - | - |
| /pemilik/paket | GET | Authenticated pemilik | 200 | Daftar paket | - | - |
| /pemilik/paket/beli | POST | Paket valid | 302 | Redirect ke checkout | - | - |
| /pemilik/edit-request | GET | Pemilik paket Basic | 200 | Daftar edit request | - | - |
| /pemilik/edit-request | POST | Data valid | 302 | Request tersimpan | - | - |

#### 8.2.5 Admin Endpoints

| Endpoint | Method | Skenario | Expected Status | Expected Response | Actual | Pass/Fail |
|----------|--------|----------|----------------|-------------------|--------|-----------|
| /admin/dashboard | GET | Authenticated admin | 200 | Dashboard HTML | - | - |
| /admin/users | GET | Authenticated admin | 200 | Daftar users | - | - |
| /admin/users/{id} | GET | User ada | 200 | Detail user | - | - |
| /admin/users/{id} | PUT | Data valid | 302 | User terupdate | - | - |
| /admin/users/{id} | DELETE | User ada | 302 | User terhapus | - | - |
| /admin/destinasi | GET | Authenticated admin | 200 | Semua destinasi | - | - |
| /admin/destinasi/{id}/approve | POST | Destinasi pending | 302 | Status=approved | - | - |
| /admin/destinasi/{id}/reject | POST | Destinasi pending | 302 | Status=rejected | - | - |
| /admin/kategori | GET | Authenticated admin | 200 | Daftar kategori | - | - |
| /admin/kategori | POST | Data valid | 302 | Kategori tersimpan | - | - |
| /admin/kategori/{id} | PUT | Data valid | 302 | Kategori terupdate | - | - |
| /admin/kategori/{id} | DELETE | Kategori ada | 302 | Kategori terhapus | - | - |
| /admin/transaksi | GET | Authenticated admin | 200 | Daftar transaksi | - | - |
| /admin/edit-request | GET | Authenticated admin | 200 | Daftar edit request | - | - |
| /admin/edit-request/{id}/approve | POST | Request pending | 302 | Request approved | - | - |
| /admin/edit-request/{id}/reject | POST | Request pending | 302 | Request rejected | - | - |
| /admin/ulasan | GET | Authenticated admin | 200 | Semua ulasan | - | - |
| /admin/ulasan/{id} | DELETE | Ulasan ada | 302 | Ulasan terhapus | - | - |
| /admin/laporan | GET | Authenticated admin | 200 | Halaman laporan | - | - |

### 8.3 Postman Test Scripts

Setiap request dalam Postman Collection dilengkapi dengan test scripts otomatis:

```javascript
// Contoh test script untuk Authentication
pm.test("Status code is 302", function () {
    pm.response.to.have.status(302);
});

pm.test("Redirect to correct dashboard", function () {
    pm.expect(pm.response.headers.get("Location")).to.include("/wisatawan/beranda");
});

// Contoh test script untuk Waybot API
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has session_token", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property("session_token");
    pm.environment.set("waybot_session_token", jsonData.session_token);
});

pm.test("Response has AI message", function () {
    const jsonData = pm.response.json();
    pm.expect(jsonData).to.have.property("response");
    pm.expect(jsonData.response).to.be.a("string");
    pm.expect(jsonData.response.length).to.be.greaterThan(0);
});
```

### 8.4 Cara Menjalankan API Tests

```bash
# Install Newman (Postman CLI)
npm install -g newman
npm install -g newman-reporter-html

# Jalankan collection dengan environment local
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --reporters cli,html \
  --reporter-html-export tests/reports/api-test-report.html

# Jalankan folder tertentu
newman run "postman/collections/WayWay API Collection" \
  --folder "🔐 Authentication" \
  --environment "postman/environments/WayWay Local.environment.yaml"

# Jalankan dengan delay antar request (untuk menghindari rate limiting)
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --delay-request 500

# Jalankan dengan iterasi data
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --iteration-data tests/data/test-data.json
```

---

## 9. PENGUJIAN KEAMANAN

### 9.1 Security Test Cases

| ID | Vulnerability | Test Method | Expected | Actual | Status |
|----|--------------|-------------|----------|--------|--------|
| SEC-001 | CSRF Protection | Submit form tanpa _token | 419 Token Mismatch | - | - |
| SEC-002 | SQL Injection | Input: `'; DROP TABLE users; --` | Input disanitasi, query aman | - | - |
| SEC-003 | XSS Prevention | Input: `<script>alert('xss')</script>` | Script di-escape di output | - | - |
| SEC-004 | Auth Bypass | GET /admin tanpa login | 302 → /login | - | - |
| SEC-005 | Role Escalation | Wisatawan akses /admin/dashboard | 403 Forbidden | - | - |
| SEC-006 | IDOR | Akses itinerary user lain | 403 atau 404 | - | - |
| SEC-007 | Midtrans Signature | Webhook dengan signature tidak valid | 403 Invalid signature | - | - |
| SEC-008 | File Upload | Upload file PHP sebagai gambar | Validation error, file ditolak | - | - |
| SEC-009 | Mass Assignment | POST dengan field tambahan | Field ekstra diabaikan | - | - |
| SEC-010 | Password Hashing | Cek field password di DB | Bcrypt hash tersimpan | - | - |
| SEC-011 | Session Fixation | Reuse session ID setelah login | Session ID baru dibuat | - | - |
| SEC-012 | Brute Force | 100 percobaan login gagal | Rate limiting aktif (jika diimplementasi) | - | - |

### 9.2 Detail Security Test Procedures

#### SEC-001: CSRF Protection
```
Prosedur:
1. Buka form login di browser
2. Inspect element, hapus input _token
3. Submit form
Ekspektasi: HTTP 419 Page Expired
```

#### SEC-002: SQL Injection
```
Prosedur:
1. Login sebagai wisatawan
2. Di search bar, masukkan: ' OR '1'='1
3. Di search bar, masukkan: '; DROP TABLE destinasi; --
4. Cek response dan database
Ekspektasi: Hasil pencarian normal atau kosong, database tidak terpengaruh
```

#### SEC-003: XSS Prevention
```
Prosedur:
1. Login sebagai wisatawan
2. Submit ulasan dengan komentar: <script>alert('XSS')</script>
3. Lihat halaman ulasan
Ekspektasi: Script tidak dieksekusi, ditampilkan sebagai teks biasa
```

#### SEC-006: IDOR (Insecure Direct Object Reference)
```
Prosedur:
1. Login sebagai wisatawan A
2. Buat itinerary, catat ID-nya (misal: /itinerary/5)
3. Login sebagai wisatawan B
4. Akses /itinerary/5 (milik wisatawan A)
Ekspektasi: 403 Forbidden atau 404 Not Found
```

### 9.3 OWASP Top 10 Checklist

| OWASP Category | Applicable | Tested | Result | Notes |
|----------------|-----------|--------|--------|-------|
| A01: Broken Access Control | ✅ | - | - | Role middleware, IDOR checks |
| A02: Cryptographic Failures | ✅ | - | - | Password hashing, HTTPS |
| A03: Injection | ✅ | - | - | Eloquent ORM, parameterized queries |
| A04: Insecure Design | ✅ | - | - | Business logic review |
| A05: Security Misconfiguration | ✅ | - | - | .env, debug mode, error pages |
| A06: Vulnerable Components | ✅ | - | - | composer audit, npm audit |
| A07: Auth & Session Failures | ✅ | - | - | Session management, logout |
| A08: Software Integrity Failures | ✅ | - | - | Composer lock, package integrity |
| A09: Logging Failures | ✅ | - | - | Laravel logging, error tracking |
| A10: SSRF | ⚠️ | - | - | AI service calls, OSRM calls |

### 9.4 Security Testing Tools

| Tool | Penggunaan |
|------|-----------|
| Browser DevTools | Inspect requests, manipulate cookies/tokens |
| Postman | Test API endpoints dengan manipulasi headers |
| `composer audit` | Cek vulnerabilities di PHP dependencies |
| `npm audit` | Cek vulnerabilities di JS dependencies |
| Manual Review | Code review untuk logika keamanan |

---

## 10. PENGUJIAN PERFORMA

### 10.1 Response Time Benchmarks

| Endpoint | Target | Actual (Avg) | Actual (95th Percentile) | Pass/Fail |
|----------|--------|-------------|--------------------------|-----------|
| GET / (Homepage) | < 2s | - | - | - |
| GET /destinasi | < 3s | - | - | - |
| GET /destinasi/{id} | < 2s | - | - | - |
| POST /login | < 2s | - | - | - |
| POST /waybot/chat | < 10s | - | - | - |
| POST /itinerary/generate | < 30s | - | - | - |
| GET /wisatawan/beranda | < 3s | - | - | - |
| GET /pemilik/dashboard | < 3s | - | - | - |
| GET /admin/dashboard | < 3s | - | - | - |
| GET /admin/users | < 5s | - | - | - |

### 10.2 Load Testing Scenarios

| Scenario | Concurrent Users | Duration | Target Pass Rate | Actual Pass Rate | Status |
|----------|-----------------|----------|-----------------|-----------------|--------|
| Normal Load | 10 | 5 menit | 99% | - | - |
| Peak Load | 50 | 5 menit | 95% | - | - |
| Stress Test | 100 | 2 menit | 80% | - | - |
| Spike Test | 0 → 100 → 0 | 1 menit | 90% | - | - |

### 10.3 Database Query Performance

| Query | Expected Time | Actual | Optimization Applied |
|-------|--------------|--------|---------------------|
| Destinasi list dengan kategori | < 100ms | - | Eager loading (with kategori) |
| Destinasi search by nama | < 200ms | - | Index pada kolom nama_destinasi |
| User dengan currentPaket | < 50ms | - | Eager loading (with currentPaket) |
| Ulasan dengan user info | < 100ms | - | Eager loading (with user) |
| Itinerary dengan destinasi | < 150ms | - | Eager loading (with destinasi) |
| Admin dashboard stats | < 500ms | - | Aggregate queries |

### 10.4 Memory Usage

| Scenario | Expected Memory | Actual | Status |
|----------|----------------|--------|--------|
| Normal page load | < 64 MB | - | - |
| AI Chatbot response | < 128 MB | - | - |
| Itinerary generation | < 256 MB | - | - |
| PDF generation | < 128 MB | - | - |
| Admin dashboard | < 64 MB | - | - |

### 10.5 Performance Testing dengan Postman

```javascript
// Test script untuk response time di Postman
pm.test("Response time is less than 3000ms", function () {
    pm.expect(pm.response.responseTime).to.be.below(3000);
});

pm.test("Response time is less than 30000ms (AI endpoints)", function () {
    pm.expect(pm.response.responseTime).to.be.below(30000);
});
```

---

## 11. RINGKASAN HASIL PENGUJIAN

### 11.1 Test Execution Summary

| Kategori Pengujian | Total TC | Pass | Fail | Skip | Pass Rate |
|-------------------|----------|------|------|------|-----------|
| Unit Testing | 33 | - | - | - | - |
| Integration Testing | 62 | - | - | - | - |
| System Testing | 140 | - | - | - | - |
| API Testing | 78 | - | - | - | - |
| Security Testing | 12 | - | - | - | - |
| **TOTAL** | **325** | **-** | **-** | **-** | **-** |

### 11.2 Test Coverage Summary

| Modul | Unit | Integration | System | API | Overall |
|-------|------|-------------|--------|-----|---------|
| Authentication | ✅ | ✅ | ✅ | ✅ | ✅ |
| Destinasi Management | ✅ | ✅ | ✅ | ✅ | ✅ |
| Paket Promosi | ✅ | ✅ | ✅ | ✅ | ✅ |
| Waybot AI | ⚠️ | ✅ | ✅ | ✅ | ✅ |
| Itinerary Planner | ⚠️ | ✅ | ✅ | ✅ | ✅ |
| Travel Agent | - | ✅ | ✅ | ✅ | ✅ |
| Admin Panel | - | ✅ | ✅ | ✅ | ✅ |
| Ulasan & Rating | ✅ | ✅ | ✅ | ✅ | ✅ |
| Payment (Midtrans) | - | ✅ | ✅ | ✅ | ✅ |
| Favorit | - | ✅ | ✅ | ✅ | ✅ |
| Edit Request | - | ✅ | ✅ | ✅ | ✅ |

**Legend:** ✅ Covered | ⚠️ Partially Covered | - Not Covered

### 11.3 Coverage Rationale

- **Waybot AI (⚠️ Partial Unit Coverage):** Unit testing untuk WaybotService terbatas karena ketergantungan pada model AI eksternal. Integration dan system testing dilakukan dengan mock responses.
- **Itinerary Planner (⚠️ Partial Unit Coverage):** ItineraryService bergantung pada OSRM API eksternal. Unit tests menggunakan mock untuk OSRM, namun coverage tidak 100%.
- **Travel Agent, Admin, Payment (- Unit Coverage):** Komponen-komponen ini lebih bersifat CRUD dan workflow, sehingga lebih efektif diuji melalui integration dan system testing.

### 11.4 Risk Assessment

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| AI service tidak tersedia | Medium | High | Fallback response mechanism |
| Midtrans API down | Low | High | Retry mechanism + manual approval |
| OSRM service timeout | Medium | Medium | Fallback ke Haversine-only routing |
| Database connection loss | Low | Critical | Connection pooling + retry |
| File upload storage penuh | Low | Medium | Storage monitoring + cleanup |
| Session hijacking | Low | High | HTTPS, secure session config |
| DDoS pada AI endpoints | Medium | High | Rate limiting, queue system |

### 11.5 Quality Metrics

| Metric | Target | Actual |
|--------|--------|--------|
| Overall Pass Rate | ≥ 85% | - |
| Unit Test Pass Rate | ≥ 90% | - |
| API Test Pass Rate | ≥ 90% | - |
| Code Coverage (kritis) | ≥ 70% | - |
| Critical Bugs | 0 | - |
| High Bugs | ≤ 2 | - |
| Avg Response Time | < 3s | - |

---

## 12. DEFECT REPORT

### 12.1 Defect Log

| ID | Tanggal | Modul | Severity | Priority | Deskripsi | Steps to Reproduce | Expected | Actual | Status | Assignee |
|----|---------|-------|----------|----------|-----------|-------------------|----------|--------|--------|----------|
| BUG-001 | - | - | Critical/High/Medium/Low | P1/P2/P3 | [Deskripsi bug] | [Langkah reproduksi] | [Hasil yang diharapkan] | [Hasil aktual] | Open/Fixed/Closed | - |

*Tabel ini akan diisi selama dan setelah eksekusi pengujian.*

### 12.2 Severity Definitions

| Severity | Definisi | Contoh pada WayWay |
|----------|----------|-------------------|
| **Critical** | Sistem tidak dapat digunakan, data loss, security breach | Login tidak berfungsi, payment error, data corruption |
| **High** | Fitur utama tidak berfungsi, workaround tidak tersedia | Waybot tidak merespons, itinerary gagal di-generate |
| **Medium** | Fitur berfungsi tapi tidak optimal, workaround tersedia | Sorting tidak akurat, PDF tidak ter-format dengan benar |
| **Low** | Masalah kosmetik atau minor, tidak mempengaruhi fungsi | Typo pada label, warna tombol tidak konsisten |

### 12.3 Priority Definitions

| Priority | Definisi | SLA Fix |
|----------|----------|---------|
| **P1** | Harus diperbaiki segera, blocking production | < 24 jam |
| **P2** | Harus diperbaiki sebelum release | < 1 minggu |
| **P3** | Diperbaiki di sprint berikutnya | < 1 bulan |

### 12.4 Defect Metrics

| Metric | Value |
|--------|-------|
| Total Defects Found | - |
| Critical Defects | - |
| High Defects | - |
| Medium Defects | - |
| Low Defects | - |
| Defects Fixed | - |
| Defects Open | - |
| Defect Density (per KLOC) | - |
| Mean Time to Fix (MTTF) | - |

### 12.5 Defect Distribution by Module

| Modul | Critical | High | Medium | Low | Total |
|-------|----------|------|--------|-----|-------|
| Authentication | - | - | - | - | - |
| Destinasi Management | - | - | - | - | - |
| Paket Promosi | - | - | - | - | - |
| Waybot AI | - | - | - | - | - |
| Itinerary Planner | - | - | - | - | - |
| Travel Agent | - | - | - | - | - |
| Admin Panel | - | - | - | - | - |
| Ulasan & Rating | - | - | - | - | - |
| Payment | - | - | - | - | - |
| **TOTAL** | **-** | **-** | **-** | **-** | **-** |

---

## 13. KESIMPULAN DAN REKOMENDASI

### 13.1 Kesimpulan

Pengujian perangkat lunak WayWay Tourism Platform telah dirancang secara komprehensif mencakup **325 test cases** di **5 level pengujian** (Unit, Integration, System, API, Security). Berikut adalah kesimpulan utama berdasarkan desain dan analisis sistem:

#### Kekuatan Sistem

1. **Arsitektur Berlapis yang Solid**
   Pemisahan concerns yang baik antara Controller, Service, dan Model memudahkan pengujian unit secara terisolasi. HaversineService, BayesianScoringService, dan GreedyRouterService diimplementasikan sebagai service terpisah yang mudah di-mock dan diuji.

2. **Keamanan Autentikasi yang Komprehensif**
   Implementasi CSRF protection (Laravel default), session-based authentication, role-based middleware, dan Google OAuth (Socialite) memberikan lapisan keamanan yang solid. Validasi signature Midtrans mencegah webhook palsu.

3. **Sistem Paket yang Konsisten**
   Batasan paket (Basic/Standard/Premium) diterapkan secara konsisten di seluruh aplikasi melalui middleware dan service layer, memudahkan pengujian dan pemeliharaan.

4. **Database Testing yang Terisolasi**
   Penggunaan SQLite in-memory untuk testing memastikan setiap test run dimulai dari state yang bersih, menghindari kontaminasi data antar test.

5. **Integrasi Payment yang Aman**
   Validasi signature Midtrans pada webhook endpoint mencegah manipulasi status pembayaran dari pihak tidak berwenang.

#### Area yang Perlu Perhatian

1. **AI Service Dependencies**
   WaybotService dan ItineraryService bergantung pada layanan eksternal yang dapat tidak tersedia. Diperlukan fallback mechanism yang robust.

2. **Test Coverage AI Components**
   Unit testing untuk komponen AI lebih sulit karena ketergantungan pada model eksternal. Perlu strategi mocking yang lebih baik.

3. **Performance Monitoring**
   Endpoint AI (Waybot, Itinerary) memerlukan monitoring response time yang ketat karena potensi latency tinggi.

4. **Rate Limiting**
   Implementasi rate limiting untuk endpoint AI dan login belum teridentifikasi dalam codebase dan perlu dipertimbangkan.

### 13.2 Rekomendasi

#### Jangka Pendek (1–2 Minggu)

1. **Implementasikan Rate Limiting**
   Tambahkan rate limiting pada endpoint `/waybot/chat` (maks 10 req/menit) dan `/itinerary/generate` (maks 5 req/menit) menggunakan Laravel Throttle middleware.

2. **Tambahkan Logging Komprehensif**
   Implementasikan logging untuk semua error pada AI services menggunakan Laravel Log facade dengan level yang tepat (error, warning, info).

3. **Buat Fallback Response untuk AI Services**
   Implementasikan try-catch dengan fallback response ketika WaybotService atau ItineraryService tidak tersedia, sehingga aplikasi tetap dapat digunakan.

4. **Optimasi Database Indexes**
   Tambahkan index pada kolom yang sering di-query: `nama_destinasi`, `kategori_id`, `user_id`, `status`, `created_at`.

#### Jangka Menengah (1–2 Bulan)

1. **Automated Testing Pipeline (CI/CD)**
   Implementasikan GitHub Actions workflow yang menjalankan `php artisan test` dan Newman secara otomatis pada setiap push ke branch utama.

2. **Application Monitoring**
   Implementasikan monitoring dengan Laravel Telescope (development) atau Sentry (production) untuk tracking error dan performance.

3. **Caching Strategy**
   Implementasikan caching untuk endpoint yang sering diakses: daftar destinasi (TTL: 5 menit), daftar kategori (TTL: 1 jam), Bayesian scores (TTL: 15 menit).

4. **API Documentation**
   Buat comprehensive API documentation menggunakan Swagger/OpenAPI 3.0 yang terintegrasi dengan codebase Laravel.

#### Jangka Panjang (3–6 Bulan)

1. **Automated Load Testing**
   Implementasikan load testing otomatis (menggunakan k6 atau Artillery) sebagai bagian dari pipeline CI/CD sebelum setiap release ke production.

2. **Microservices untuk AI Components**
   Pertimbangkan memisahkan WaybotService dan ItineraryService menjadi microservices terpisah untuk skalabilitas dan fault isolation yang lebih baik.

3. **Database Read Replicas**
   Implementasikan database read replicas untuk query-heavy operations (listing destinasi, admin reports) guna meningkatkan performa.

4. **Disaster Recovery Plan**
   Buat dan dokumentasikan disaster recovery plan termasuk backup strategy, RTO (Recovery Time Objective), dan RPO (Recovery Point Objective).

### 13.3 Persetujuan Dokumen

| Peran | Nama | Tanda Tangan | Tanggal |
|-------|------|-------------|---------|
| Pembuat Laporan | [Nama Mahasiswa] | | |
| Reviewer | [Nama Dosen/Supervisor] | | |
| Approver | [Nama Dosen Pembimbing] | | |

---

## APPENDIX A: Test Execution Commands

```bash
# ============================================================
# SETUP TESTING ENVIRONMENT
# ============================================================

# Copy environment file untuk testing
cp .env .env.testing

# Generate application key untuk testing
php artisan key:generate --env=testing

# Verifikasi konfigurasi testing
php artisan config:show --env=testing

# ============================================================
# RUNNING PHP UNIT & FEATURE TESTS
# ============================================================

# Jalankan semua tests
php artisan test

# Jalankan dengan coverage report (terminal)
php artisan test --coverage

# Jalankan dengan minimum coverage threshold
php artisan test --coverage --min=70

# Jalankan hanya Unit tests
php artisan test --testsuite=Unit

# Jalankan hanya Feature tests
php artisan test --testsuite=Feature

# Jalankan test files spesifik
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/DestinasiTest.php
php artisan test tests/Feature/WaybotTest.php
php artisan test tests/Feature/ItineraryTest.php
php artisan test tests/Feature/TravelAgentTest.php
php artisan test tests/Feature/UlasanTest.php
php artisan test tests/Feature/AdminTest.php

# Jalankan Unit test files spesifik
php artisan test tests/Unit/HaversineServiceTest.php
php artisan test tests/Unit/BayesianScoringServiceTest.php
php artisan test tests/Unit/ItineraryServiceTest.php
php artisan test tests/Unit/PaketPromosiTest.php
php artisan test tests/Unit/UserRolePermissionsTest.php
php artisan test tests/Unit/ValidationRulesTest.php

# Jalankan dengan filter nama test method
php artisan test --filter="test_register_wisatawan_valid"

# Jalankan parallel (lebih cepat)
php artisan test --parallel

# Output verbose
php artisan test --verbose

# Generate HTML coverage report
php artisan test --coverage-html tests/reports/coverage/

# ============================================================
# RUNNING POSTMAN API TESTS (NEWMAN)
# ============================================================

# Install Newman
npm install -g newman
npm install -g newman-reporter-html
npm install -g newman-reporter-junit

# Jalankan collection dengan environment local
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --reporters cli,html \
  --reporter-html-export tests/reports/api-test-report.html

# Jalankan dengan JUnit report (untuk CI/CD)
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --reporters cli,junit \
  --reporter-junit-export tests/reports/api-test-results.xml

# Jalankan folder tertentu
newman run "postman/collections/WayWay API Collection" \
  --folder "🔐 Authentication" \
  --environment "postman/environments/WayWay Local.environment.yaml"

newman run "postman/collections/WayWay API Collection" \
  --folder "🤖 Waybot AI" \
  --environment "postman/environments/WayWay Local.environment.yaml"

newman run "postman/collections/WayWay API Collection" \
  --folder "👑 Admin" \
  --environment "postman/environments/WayWay Local.environment.yaml"

# Jalankan dengan delay antar request
newman run "postman/collections/WayWay API Collection" \
  --environment "postman/environments/WayWay Local.environment.yaml" \
  --delay-request 500

# ============================================================
# SECURITY CHECKS
# ============================================================

# Cek vulnerabilities PHP dependencies
composer audit

# Cek vulnerabilities JS dependencies
npm audit

# Fix JS vulnerabilities (jika ada)
npm audit fix
```

---

## APPENDIX B: Test File Structure

```
tests/
├── Feature/
│   ├── AuthTest.php                    (15 tests)
│   │   ├── test_register_wisatawan_valid
│   │   ├── test_register_duplicate_email
│   │   ├── test_register_password_mismatch
│   │   ├── test_login_wisatawan
│   │   ├── test_login_admin
│   │   ├── test_login_wrong_password
│   │   └── ... (9 more tests)
│   │
│   ├── DestinasiTest.php               (12 tests)
│   │   ├── test_create_destinasi_basic
│   │   ├── test_exceed_basic_limit
│   │   ├── test_edit_destinasi_standard
│   │   ├── test_delete_destinasi
│   │   └── ... (8 more tests)
│   │
│   ├── WaybotTest.php                  (7 tests)
│   │   ├── test_first_waybot_message
│   │   ├── test_continue_conversation
│   │   ├── test_empty_message_validation
│   │   ├── test_history_retrieval
│   │   ├── test_reset_session
│   │   └── ... (2 more tests)
│   │
│   ├── ItineraryTest.php               (8 tests)
│   │   ├── test_generate_itinerary
│   │   ├── test_save_itinerary
│   │   ├── test_download_pdf
│   │   ├── test_delete_itinerary
│   │   └── ... (4 more tests)
│   │
│   ├── TravelAgentTest.php             (9 tests)
│   ├── UlasanTest.php                  (7 tests)
│   └── AdminTest.php                   (11 tests)
│
├── Unit/
│   ├── HaversineServiceTest.php        (6 tests)
│   ├── BayesianScoringServiceTest.php  (5 tests)
│   ├── ItineraryServiceTest.php        (5 tests)
│   ├── PaketPromosiTest.php            (5 tests)
│   ├── UserRolePermissionsTest.php     (5 tests)
│   ├── ValidationRulesTest.php         (7 tests)
│   └── UnitTestRecommendations.md
│
└── documentation/
    ├── integration-testing-scenarios.md
    ├── system-testing-scenarios.md
    └── software-testing-report.md      ← Dokumen ini

postman/
├── collections/
│   └── WayWay API Collection/
│       ├── 🔐 Authentication/
│       │   ├── Register - Wisatawan.request.yaml
│       │   ├── Login - Wisatawan.request.yaml
│       │   └── ... (7 more requests)
│       ├── 🤖 Waybot AI/
│       │   ├── Chat - New Session.request.yaml
│       │   └── ... (4 more requests)
│       ├── 👑 Admin/
│       │   └── ... (19 requests)
│       └── ... (other folders)
└── environments/
    ├── WayWay Local.environment.yaml
    └── WayWay Staging.environment.yaml

tests/reports/                          (generated)
├── api-test-report.html
├── api-test-results.xml
└── coverage/
    └── index.html
```

---

## APPENDIX C: Glossary

| Term | Definition |
|------|------------|
| **TC** | Test Case — Skenario pengujian individual dengan input, langkah, dan expected result yang terdefinisi |
| **SYS** | System Test — Pengujian end-to-end dari perspektif pengguna |
| **INT** | Integration Test — Pengujian interaksi antar komponen/modul |
| **UNIT** | Unit Test — Pengujian komponen individual secara terisolasi |
| **SEC** | Security Test — Pengujian aspek keamanan sistem |
| **CSRF** | Cross-Site Request Forgery — Serangan yang memaksa pengguna melakukan aksi tidak diinginkan |
| **XSS** | Cross-Site Scripting — Injeksi script berbahaya ke halaman web |
| **IDOR** | Insecure Direct Object Reference — Akses langsung ke objek tanpa otorisasi |
| **OWASP** | Open Web Application Security Project — Organisasi yang mendokumentasikan kerentanan keamanan web |
| **PHPUnit** | Framework pengujian unit untuk PHP |
| **Newman** | Postman CLI Runner — Tool untuk menjalankan Postman Collection dari command line |
| **Haversine** | Formula matematika untuk menghitung jarak antara dua titik di permukaan bola bumi |
| **Bayesian** | Metode scoring berbasis probabilitas Bayesian untuk menghasilkan ranking yang adil |
| **OSRM** | Open Source Routing Machine — Engine routing open-source untuk optimasi rute |
| **Greedy Router** | Algoritma optimasi rute berbasis pendekatan greedy (pilih destinasi terdekat berikutnya) |
| **Midtrans** | Payment gateway Indonesia yang digunakan untuk memproses pembayaran paket promosi |
| **Snap** | Produk Midtrans yang menyediakan UI pembayaran siap pakai |
| **Webhook** | HTTP callback yang dikirim Midtrans ke server untuk notifikasi status pembayaran |
| **Socialite** | Package Laravel untuk OAuth authentication (Google, Facebook, dll) |
| **Eloquent** | ORM (Object-Relational Mapping) bawaan Laravel |
| **Middleware** | Layer yang memproses HTTP request sebelum mencapai controller |
| **Seeder** | Script untuk mengisi database dengan data awal/test |
| **Factory** | Class untuk membuat model instances dengan data dummy untuk testing |
| **SQLite** | Database ringan berbasis file, digunakan untuk testing in-memory |
| **CI/CD** | Continuous Integration/Continuous Deployment — Pipeline otomasi build, test, dan deploy |
| **KLOC** | Kilo Lines of Code — Satuan ukuran kode (1000 baris kode) |
| **RTO** | Recovery Time Objective — Target waktu pemulihan sistem setelah gangguan |
| **RPO** | Recovery Point Objective — Target titik data yang dapat dipulihkan setelah gangguan |

---

*Dokumen ini dibuat sebagai bagian dari persyaratan akademik pengembangan sistem WayWay Tourism Platform.*

*Versi Dokumen: 1.0.0 | Status: Draft*
