# MoodBrew - Cafe That Understands You

MoodBrew adalah sistem manajemen cafe berbasis AI yang memberikan pengalaman personal kepada pelanggan melalui deteksi mood dan rekomendasi minuman yang tepat.

---

## DAFTAR ISI

1. Tentang MoodBrew
2. Fitur Utama
3. Teknologi yang Digunakan
4. Persyaratan Sistem
5. Instalasi
6. Konfigurasi Environment (.env)
7. Integrasi AI (Kolosal AI)
8. Menjalankan Aplikasi
9. Panduan Penggunaan
10. Struktur Project
11. Akun Default
12. Troubleshooting

---

## 1. TENTANG MOODBREW

MoodBrew adalah aplikasi cafe management yang mengintegrasikan AI untuk:

- Mendeteksi mood pelanggan dari input text
- Memberikan rekomendasi minuman berdasarkan mood
- Menyediakan "Silent Social Wall" untuk berbagi vibe secara anonim
- Membantu staff memahami kondisi emosional pelanggan (Empathy Radar)
- Generate copywriting flash sale secara otomatis

### Masalah yang Diselesaikan

1. Pelanggan sering kesulitan memilih menu yang sesuai dengan mood mereka
2. Cafe kehilangan kesempatan untuk memberikan layanan yang lebih personal
3. Customer service tidak bisa mengetahui kondisi emosional pelanggan secara real-time

### Solusi

1. AI Chat yang bisa menganalisis mood dan memberikan rekomendasi menu
2. 8 kategori mood spesifik: Happy, Relaxed, Energetic, Tired, Stressed, Loved, Thoughtful, Coffee Time
3. Empathy Radar untuk staff melihat mood pelanggan sebelum melayani
4. Silent Social Wall untuk pelanggan berbagi perasaan secara anonim

---

## 2. FITUR UTAMA

### Fitur AI

1. Mood-Based Recommendation - AI menganalisis mood dan merekomendasikan menu yang cocok
2. Sentiment Analysis - Klasifikasi otomatis mood pelanggan menjadi 8 kategori
3. AI Copywriting - Generate flash sale copy secara otomatis
4. AI Chat Interface - Percakapan interaktif dengan AI untuk mendapat rekomendasi

### Fitur Customer

1. Quick Access Login - Masuk tanpa password (hanya nama dan email)
2. AI Chat - Ngobrol dengan AI untuk mendapat rekomendasi minuman
3. Public Menu - Lihat menu tanpa perlu login
4. Public Vibe Wall - Lihat vibe wall tanpa login
5. Cart dan Checkout - Keranjang belanja dengan integrasi flash sale
6. Table-Based Ordering - Scan QR code di meja untuk langsung order

### Fitur Admin

1. Dashboard Analytics - Statistik penjualan dan performa
2. Staff Management - CRUD data staff (kasir)
3. Menu Management - CRUD data menu dengan flavor profile
4. Order Management - Monitor semua pesanan
5. Vibe Wall Moderation - Approve/reject vibe entries
6. Sales Reports - Laporan harian dan bulanan

### Fitur Cashier

1. Empathy Radar Dashboard - Lihat mood pelanggan sebelum deliver order
2. Flash Sale Management - Buat flash sale dengan AI-generated copy
3. Order Processing - Proses pesanan (pending, preparing, completed)
4. Payment Processing - Cash, QRIS, Debit

---

## 3. TEKNOLOGI YANG DIGUNAKAN

### Backend
- Laravel 12
- PHP 8.2+
- MySQL/MariaDB

### Frontend
- Alpine.js (reactive components)
- Tailwind CSS (styling)
- Blade Templates (server-side rendering)

### AI Integration
- **Kolosal AI** dengan model **Qwen 3 30BA3B**
- OpenAI-compatible API format
- Optimized untuk bahasa Indonesia

---

## 4. PERSYARATAN SISTEM

1. PHP >= 8.2
2. Composer
3. MySQL atau MariaDB
4. Node.js dan NPM
5. Git

---

## 5. INSTALASI

### 5.1 Clone Repository

```bash
git clone https://github.com/TonAhmad/MoodBrew.git
cd MoodBrew
```

### 5.2 Install PHP Dependencies

```bash
composer install
```

### 5.3 Install Node Dependencies

```bash
npm install
```

### 5.4 Copy Environment File

```bash
cp .env.example .env
```

### 5.5 Generate Application Key

```bash
php artisan key:generate
```

### 5.6 Buat Database

Buat database baru di MySQL dengan nama `moodbrew`:

```sql
CREATE DATABASE moodbrew;
```

### 5.7 Jalankan Migration dan Seeder

```bash
php artisan migrate --seed
```

### 5.8 Build Assets

```bash
npm run build
```

Atau untuk development dengan hot reload:

```bash
npm run dev
```

---

## 6. KONFIGURASI ENVIRONMENT (.env)

Buka file `.env` dan sesuaikan konfigurasi berikut:

### 6.1 Konfigurasi Aplikasi

```env
APP_NAME=MoodBrew
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
```

### 6.2 Konfigurasi Database

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moodbrew
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan `DB_USERNAME` dan `DB_PASSWORD` dengan kredensial MySQL Anda.

### 6.3 Konfigurasi Session

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

### 6.4 Konfigurasi AI (Kolosal AI)

```env
AI_PROVIDER=kolosal
AI_API_KEY=your-kolosal-api-key-here
AI_API_URL=https://api.kolosal.ai/v1
AI_MODEL="Qwen 3 30BA3B"
AI_TEMPERATURE=0.7
AI_MAX_TOKENS=1000
AI_ENABLED=true
AI_FALLBACK_ENABLED=true
```

⚡ **MODEL AI YANG DIGUNAKAN: Qwen 3 30BA3B** ⚡

PENTING:
- `AI_API_KEY`: Dapatkan dari https://kolosal.ai setelah registrasi
- `AI_MODEL`: **Kami menggunakan "Qwen 3 30BA3B" dari Kolosal AI** untuk performa optimal dalam bahasa Indonesia
- `AI_ENABLED`: Set `true` untuk mengaktifkan fitur AI, `false` untuk menonaktifkan
- Gunakan tanda kutip jika nama model mengandung spasi

### 6.5 Contoh .env Lengkap

```env
APP_NAME=MoodBrew
APP_ENV=local
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moodbrew
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@moodbrew.com"
MAIL_FROM_NAME="${APP_NAME}"

AI_PROVIDER=kolosal
AI_API_KEY=your-kolosal-api-key-here
AI_API_URL=https://api.kolosal.ai/v1
AI_MODEL="Qwen 3 30BA3B"
AI_TEMPERATURE=0.7
AI_MAX_TOKENS=1000
AI_ENABLED=true
AI_FALLBACK_ENABLED=true

VITE_APP_NAME="${APP_NAME}"
```

---

## 7. INTEGRASI AI (KOLOSAL AI)

### 7.1 Mendapatkan API Key

1. Kunjungi https://kolosal.ai
2. Daftar atau login ke akun Anda
3. Buka menu API Keys atau Dashboard
4. Buat API Key baru
5. Copy API Key yang diberikan

### 7.2 Memasukkan API Key ke .env

Buka file `.env` dan masukkan API Key:

```env
AI_API_KEY=kol_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### 7.3 Memilih Model AI

Kolosal AI menyediakan beberapa model. **MoodBrew menggunakan Qwen 3 30BA3B** untuk hasil terbaik dalam bahasa Indonesia:

```env
AI_MODEL="Qwen 3 30BA3B"
```

Model lain yang tersedia di Kolosal AI:
- Claude Sonnet 4.5
- meta-llama/llama-4-maverick-17b-128e-instruct
- Dan model lain yang tersedia di Kolosal AI

CATATAN: Jika nama model mengandung spasi, gunakan tanda kutip.

### 7.4 Mengatur Parameter AI

1. `AI_TEMPERATURE`: Nilai 0.0 - 1.0. Semakin tinggi, respons AI semakin kreatif/random
2. `AI_MAX_TOKENS`: Maksimum panjang respons AI (1000 sudah cukup untuk chat)
3. `AI_ENABLED`: Set `true` untuk mengaktifkan, `false` untuk menonaktifkan

### 7.5 Verifikasi Integrasi

Setelah konfigurasi, jalankan aplikasi dan akses halaman Customer. Jika AI aktif, Anda akan melihat chat interface. Jika AI tidak aktif atau API Key salah, akan muncul banner "AI Sedang Maintenance".

---

## 8. MENJALANKAN APLIKASI

### 8.1 Development Server

```bash
php artisan serve
```

Aplikasi akan berjalan di http://127.0.0.1:8000

### 8.2 Menjalankan Vite (untuk development)

Di terminal terpisah, jalankan:

```bash
npm run dev
```

### 8.3 Cache Configuration (Opsional untuk Production)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8.4 Clear Cache

Jika ada perubahan di .env atau config:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 9. PANDUAN PENGGUNAAN

### 9.1 Untuk Customer

1. AKSES APLIKASI
   - Buka browser dan akses http://127.0.0.1:8000
   - Anda akan melihat landing page MoodBrew

2. MELIHAT MENU (TANPA LOGIN)
   - Klik menu "Menu" di navbar
   - Lihat semua menu yang tersedia beserta harga dan deskripsi

3. MELIHAT VIBE WALL (TANPA LOGIN)
   - Klik menu "Vibe Wall" di navbar
   - Lihat vibe/mood yang dibagikan pelanggan lain

4. MULAI MEMESAN
   - Klik tombol "Mulai Pesan" di landing page atau navbar
   - Isi nama panggilan dan email
   - Klik "Mulai Pesan dengan AI"

5. CHAT DENGAN AI
   - Setelah login, Anda akan masuk ke halaman AI Chat
   - Ketik mood Anda, contoh: "Saya lagi ngantuk banget"
   - AI akan memberikan rekomendasi minuman yang cocok
   - Atau klik tombol Quick Mood untuk pilihan cepat

6. MENAMBAH KE KERANJANG
   - Dari rekomendasi AI, klik "Tambah ke Keranjang"
   - Atau kunjungi halaman Menu untuk pilih manual
   - Di halaman Menu, klik item untuk melihat detail
   - Pilih quantity dan klik "Tambah ke Keranjang"

7. CHECKOUT
   - Klik icon keranjang di navbar
   - Review pesanan Anda
   - Isi nomor meja (opsional)
   - Klik "Checkout"

8. PEMBAYARAN
   - Pesanan akan masuk ke sistem kasir
   - Bayar di kasir saat pesanan siap (Cash/QRIS/Debit)

9. BERBAGI VIBE
   - Klik menu "Vibe Wall"
   - Klik "Bagikan Vibe Kamu"
   - Tulis perasaan/mood Anda
   - Pilih emoji yang sesuai
   - Pilih apakah ingin anonim atau tampilkan nama
   - Klik "Kirim"
   - Vibe akan muncul setelah diapprove admin

### 9.2 Untuk Staff (Admin)

1. LOGIN
   - Akses http://127.0.0.1:8000/staff/login
   - Login dengan email: admin@moodbrew.com, password: password

2. DASHBOARD
   - Lihat statistik: total order, revenue, customer, menu aktif
   - Lihat chart penjualan
   - Lihat order terbaru

3. MENU MANAGEMENT
   - Klik "Menu" di sidebar
   - Untuk tambah menu: klik "Tambah Menu"
   - Isi nama, kategori, harga, deskripsi, flavor profile
   - Untuk edit: klik tombol edit pada item
   - Untuk toggle availability: klik toggle pada item

4. STAFF MANAGEMENT
   - Klik "Staff" di sidebar
   - Tambah kasir baru dengan klik "Tambah Staff"
   - Edit atau hapus staff yang ada

5. ORDER MANAGEMENT
   - Klik "Orders" di sidebar
   - Lihat semua pesanan dengan filter status
   - Klik order untuk melihat detail

6. VIBE WALL MODERATION
   - Klik "Vibe Wall" di sidebar
   - Tab "Pending" untuk review vibe baru
   - Approve atau reject vibe
   - Tab "Semua" untuk lihat semua vibe

7. REPORTS
   - Klik "Reports" di sidebar
   - Pilih laporan harian atau bulanan
   - Lihat statistik penjualan

### 9.3 Untuk Staff (Cashier)

1. LOGIN
   - Akses http://127.0.0.1:8000/staff/login
   - Login dengan email: cashier@moodbrew.com, password: password

2. DASHBOARD
   - Lihat order yang perlu diproses
   - Lihat statistik harian

3. ORDER PROCESSING
   - Klik "Orders" di sidebar
   - Tab "Pending" untuk order baru
   - Tab "Preparing" untuk order yang sedang dibuat
   - Tab "Completed" untuk order selesai
   - Klik order untuk melihat detail dan update status

4. PROCESS PAYMENT
   - Di halaman Pending Orders, klik order
   - Pilih metode pembayaran: Cash, QRIS, atau Debit
   - Untuk Cash: masukkan jumlah uang yang diterima, sistem akan hitung kembalian
   - Klik "Proses Pembayaran"

5. EMPATHY RADAR
   - Klik "Empathy Radar" di sidebar
   - Lihat mood summary pelanggan yang sedang order
   - Gunakan informasi ini untuk memberikan service yang lebih personal

6. FLASH SALE
   - Klik "Flash Sale" di sidebar
   - Buat flash sale baru dengan klik "Buat Flash Sale"
   - Pilih menu item
   - Set persentase diskon
   - Set waktu mulai dan berakhir
   - Klik "Generate AI Copy" untuk buat copywriting otomatis
   - Atau tulis copy manual
   - Klik "Simpan"

7. MENU MANAGEMENT
   - Cashier juga bisa manage menu
   - Klik "Menu" di sidebar
   - Toggle availability untuk menu yang habis

---

## 10. STRUKTUR PROJECT

```
moodbrew/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/           # Controller untuk admin
│   │   │   ├── Cashier/         # Controller untuk cashier
│   │   │   ├── Customer/        # Controller untuk customer
│   │   │   ├── AuthController.php
│   │   │   └── LandingController.php
│   │   ├── Middleware/
│   │   │   └── RoleMiddleware.php
│   │   └── Requests/            # Form request validation
│   ├── Models/                  # Eloquent models
│   ├── Services/                # Business logic layer
│   │   ├── AI/                  # AI services
│   │   │   ├── AiChatService.php
│   │   │   ├── AiCopywritingService.php
│   │   │   ├── AiRecommendationService.php
│   │   │   └── AiSentimentService.php
│   │   ├── AuthService.php
│   │   ├── CartService.php
│   │   ├── OrderService.php
│   │   └── ...
│   └── Providers/
├── config/
│   ├── app.php
│   ├── services.php             # Konfigurasi AI ada di sini
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── admin/              # Views untuk admin
│   │   ├── cashier/            # Views untuk cashier
│   │   ├── customer/           # Views untuk customer
│   │   ├── landing/            # Views untuk landing page
│   │   ├── auth/               # Views untuk login
│   │   ├── components/         # Shared components
│   │   └── layouts/            # Layout templates
│   ├── css/
│   └── js/
├── routes/
│   └── web.php                 # Semua routes
├── .env                        # Environment configuration
├── .env.example                # Template environment
├── composer.json
├── package.json
└── README.md
```

---

## 11. AKUN DEFAULT

Setelah menjalankan `php artisan migrate --seed`, akun berikut akan dibuat:

### 🔐 AKUN UNTUK JURI HACKATHON

#### 👨‍💼 Admin
- **Email**: `admin@moodbrew.id`
- **Password**: `admin123`
- **Role**: Admin
- **URL Login**: http://127.0.0.1:8000/staff/login

#### 💰 Cashier
- **Email**: `kasir1@moodbrew.id`
- **Password**: `kasir123`
- **Role**: Cashier
- **URL Login**: http://127.0.0.1:8000/staff/login

Atau gunakan kasir kedua:
- **Email**: `kasir2@moodbrew.id`
- **Password**: `kasir123`

#### 👤 Customer (Opsional - untuk testing)
**Customer 1:**
- **Email**: `budi@email.com`
- **Password**: `customer123`
- **URL Login**: http://127.0.0.1:8000/customer/login

**Customer 2:**
- **Email**: `sari@email.com`
- **Password**: `customer123`

---

### ⚠️ CATATAN PENTING UNTUK JURI

1. **Untuk Pengalaman Customer Terbaik**: 
   - Kami sarankan menggunakan fitur **Quick Access Login** (tanpa password)
   - Cukup masukkan nama dan email di landing page
   - Langsung chat dengan AI dan dapatkan rekomendasi minuman

2. **Login Staff**:
   - Admin dan Cashier harus login dengan email dan password
   - Akses URL: http://127.0.0.1:8000/staff/login

3. **Model AI**: 
   - Aplikasi menggunakan **Qwen 3 30BA3B** dari Kolosal AI
   - Pastikan `AI_ENABLED=true` di file `.env`

4. **Password Default**:
   - Ubah password default sebelum deploy ke production
   - Ini hanya untuk keperluan demo dan testing

---

## 12. TROUBLESHOOTING

### 12.1 Error: Class Not Found

Solusi:
```bash
composer dump-autoload
```

### 12.2 Error: Table Not Found

Solusi:
```bash
php artisan migrate:fresh --seed
```

PERINGATAN: Ini akan menghapus semua data di database.

### 12.3 Error: AI Tidak Merespons

Cek:
1. Pastikan `AI_API_KEY` sudah benar di .env
2. Pastikan `AI_ENABLED=true`
3. Cek koneksi internet
4. Clear config cache: `php artisan config:clear`

### 12.4 Error: Session Expired / Login Tidak Bisa

Solusi:
```bash
php artisan session:table
php artisan migrate
php artisan config:clear
```

### 12.5 Error: Styles Tidak Muncul

Solusi:
```bash
npm run build
```

### 12.6 Error: Environment File Invalid (Whitespace)

Jika ada error seperti "Unexpected whitespace", pastikan value yang mengandung spasi dibungkus dengan tanda kutip:

Salah:
```env
AI_MODEL=Claude Sonnet 4.5
```

Benar:
```env
AI_MODEL="Claude Sonnet 4.5"
```

### 12.7 Clear Semua Cache

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## LICENSE

This project is licensed under the MIT License.

---

## KONTRIBUTOR

- TonAhmad - Full Stack Developer

---

Untuk pertanyaan atau bantuan, silakan buat Issue di repository ini.
