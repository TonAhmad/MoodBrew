# ☕ MoodBrew - Coffee That Understands You

**MoodBrew** adalah sistem cafe management berbasis AI yang memberikan pengalaman personal kepada pelanggan melalui deteksi mood dan rekomendasi minuman yang tepat. Dikembangkan untuk [Nama Hackathon] dengan fokus pada innovation dan user experience.

![MoodBrew Banner](https://via.placeholder.com/1200x400/8B4513/FFFFFF?text=MoodBrew+-+AI+Powered+Coffee+Experience)

## 🎯 Problem & Solution

### Problem
- Pelanggan sering kesulitan memilih menu yang sesuai dengan mood mereka
- Cafe kehilangan kesempatan untuk memberikan layanan yang lebih personal
- Customer service tidak bisa mengetahui kondisi emosional pelanggan secara real-time

### Solution
MoodBrew menggunakan **AI (Kolosal AI)** untuk:
1. **Mood Detection** - Menganalisis text input pelanggan untuk mendeteksi mood (8 kategori: Happy, Relaxed, Energetic, Tired, Stressed, Loved, Thoughtful, Coffee Time)
2. **Smart Recommendation** - Memberikan rekomendasi menu berdasarkan mood dan flavor profile
3. **Empathy Radar** - Dashboard untuk cashier/barista melihat mood summary pelanggan
4. **Silent Social Wall** - Platform untuk pelanggan berbagi vibe secara anonim dengan sentiment analysis
5. **AI Flash Sale** - Generate copywriting promo secara otomatis

## ✨ Key Features

### 🤖 AI-Powered Features
- **Mood-Based Recommendation** - AI menganalisis mood dan merekomendasikan menu yang cocok
- **Sentiment Analysis** - Klasifikasi otomatis vibe/mood pelanggan menjadi 8 kategori spesifik
- **AI Copywriting** - Generate flash sale copy secara otomatis dengan prompting yang teroptimasi
- **Empathy Radar** - Real-time mood dashboard untuk staff

### 📱 Customer Features
- **Quick Access Login** - Masuk tanpa password (hanya nama + email)
- **AI Chat Interface** - Ngobrol dengan AI untuk mendapat rekomendasi
- **Public Menu & Vibe Wall** - Lihat menu dan vibe wall tanpa login
- **Cart & Checkout** - Keranjang belanja dengan flash sale integration
- **Table-Based Ordering** - Scan QR code di meja untuk langsung order

### 💼 Staff Features (Admin & Cashier)
- **Empathy Radar Dashboard** - Lihat mood pelanggan sebelum deliver order
- **Flash Sale Management** - Buat flash sale dengan AI-generated copy
- **Order Management** - Track status: pending → preparing → completed
- **Vibe Wall Moderation** - Approve/reject vibe entries
- **Sales Reports** - Daily/Monthly analytics

## 🏗️ Tech Stack

### Backend
- **Laravel 12** - Clean architecture dengan service layer pattern
- **PHP 8.2+** - Modern PHP with type hints
- **MySQL** - Relational database
- **Kolosal AI** - AI/ML integration untuk recommendation & sentiment analysis

### Frontend
- **Alpine.js** - Reactive components tanpa build step
- **Tailwind CSS** - Utility-first styling
- **Blade Templates** - Server-side rendering

### Architecture
```
app/
├── Http/
│   ├── Controllers/        # Thin controllers (hanya routing)
│   │   ├── Admin/
│   │   ├── Cashier/
│   │   ├── Customer/
│   │   └── LandingController.php
│   └── Requests/          # Form validation
├── Services/              # Business logic layer
│   ├── AI/               # AI services (Recommendation, Sentiment, Chat, Copywriting)
│   ├── AuthService.php
│   ├── OrderService.php
│   └── ...
└── Models/               # Eloquent models
```

## 🚀 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Node.js & NPM (untuk assets compilation)

### Step-by-Step

1. **Clone Repository**
```bash
git clone https://github.com/[your-username]/moodbrew.git
cd moodbrew
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database** (edit `.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=moodbrew
DB_USERNAME=root
DB_PASSWORD=
```

5. **Configure AI Service** (edit `.env`)
```env
KOLOSAL_API_KEY=your_kolosal_api_key_here
KOLOSAL_API_URL=https://api.kolosal.com
```

6. **Run Migrations & Seeders**
```bash
php artisan migrate --seed
```

7. **Build Assets**
```bash
npm run build
# atau untuk development:
npm run dev
```

8. **Start Development Server**
```bash
php artisan serve
```

9. **Access Application**
- Landing Page: http://127.0.0.1:8000
- Customer Login: http://127.0.0.1:8000/login
- Staff Login: http://127.0.0.1:8000/staff/login

### Default Credentials

**Admin:**
- Email: `admin@moodbrew.com`
- Password: `password`

**Cashier:**
- Email: `cashier@moodbrew.com`
- Password: `password`

## 📸 Screenshots

### Landing Page
![Landing Page](https://via.placeholder.com/800x450/8B4513/FFFFFF?text=Landing+Page)

### AI Chat Interface (Customer)
![AI Chat](https://via.placeholder.com/800x450/8B4513/FFFFFF?text=AI+Chat+Interface)

### Empathy Radar Dashboard (Cashier)
![Empathy Radar](https://via.placeholder.com/800x450/8B4513/FFFFFF?text=Empathy+Radar)

### Silent Social Wall
![Vibe Wall](https://via.placeholder.com/800x450/8B4513/FFFFFF?text=Silent+Social+Wall)

## 🎮 Usage Guide

### For Customers

1. **Quick Start**
   - Kunjungi homepage
   - Klik "Mulai Pesan"
   - Isi nama & email (tanpa password!)

2. **Get Recommendation**
   - Chat dengan AI: "Lagi stress nih, rekomendasiin dong"
   - AI akan menganalisis mood dan memberikan rekomendasi

3. **Order & Checkout**
   - Tambahkan item ke cart
   - Checkout dan pilih nomor meja
   - Bayar di kasir saat pesanan siap

4. **Share Your Vibe**
   - Kunjungi Vibe Wall
   - Tulis mood/vibe kamu hari ini
   - Pilih anonymous atau tampilkan nama
   - AI akan mengklasifikasikan mood kamu

### For Staff

1. **Login**
   - Akses `/staff/login`
   - Login dengan email & password

2. **Admin - Manage Flash Sale**
   - Buat flash sale baru
   - Pilih menu & discount
   - Klik "Generate AI Copy" untuk copywriting otomatis

3. **Cashier - Process Orders**
   - View pending orders
   - Check Empathy Radar untuk mood pelanggan
   - Update status: preparing → completed
   - Pilih payment method: Cash/QRIS/Debit

## 🏆 Innovation Highlights

1. **8-Category Mood Classification** - Bukan sekadar positive/neutral/negative, tapi spesifik ke 8 mood dengan emoji matching
2. **Context-Aware Recommendation** - AI mempertimbangkan flavor profile menu dengan mood pelanggan
3. **Empathy-Driven Service** - Staff bisa deliver service yang lebih personal dengan melihat mood summary
4. **No-Password Customer Access** - Frictionless UX untuk pelanggan (sesuai rules hackathon)
5. **AI Copywriting Integration** - Generate promo copy otomatis, bukan CRUD biasa

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=OrderServiceTest
```

## 📁 Project Structure

```
moodbrew/
├── app/
│   ├── Http/Controllers/   # Thin controllers
│   ├── Services/          # Business logic
│   ├── Models/            # Eloquent models
│   └── ...
├── resources/
│   ├── views/             # Blade templates
│   │   ├── landing/
│   │   ├── customer/
│   │   ├── cashier/
│   │   └── admin/
│   └── css/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── web.php           # All routes (structured by role)
└── README.md
```

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **[Your Name]** - Full Stack Developer
- **Kolosal AI** - AI/ML Integration Partner

## 🙏 Acknowledgments

- Laravel Framework
- Kolosal AI API
- Tailwind CSS
- Alpine.js
- [Hackathon Name] Committee

---

**Built with ❤️ and ☕ for [Hackathon Name]**


In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
