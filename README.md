<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">🅿️ ParkirApp</h1>
<h3 align="center">Smart Parking Management System</h3>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/License-FGT-blue?style=for-the-badge" alt="License">
</p>

<p align="center">
  Sistem manajemen parkir cerdas berbasis Laravel — skalabel, real-time, dan siap produksi.<br>
  Dirancang untuk smart-city, IoT gate controller, dan aplikasi mobile modern.
</p>

---

## 📖 Tentang Proyek

**ParkirApp** adalah referensi arsitektur open-source yang dibangun di atas **Laravel Framework**, dirancang khusus untuk menangani:

- Manajemen parkir berbasis waktu secara real-time
- Alokasi slot dinamis dengan pencegahan race condition
- Perhitungan tarif otomatis multi-tier untuk berbagai jenis kendaraan
- Integrasi API untuk aplikasi Flutter/mobile dan kontroler gerbang IoT

Repositori ini berfungsi sebagai *blueprint* siap produksi bagi para developer yang memerlukan sistem tiket berkonkurensi tinggi, query database yang dioptimalkan untuk kapasitas live, serta kalkulasi tarif kendaraan yang kompleks.

---

## 🚀 Fitur Unggulan

| Fitur | Deskripsi |
|-------|-----------|
| ⚡ **Real-Time Tariff Engine** | Logika backend canggih untuk menghitung tarif progresif per jam lintas multi-tier kendaraan (Mobil, Motor, Truk) |
| 🅿️ **Optimized Slot Allocation** | Skema database yang efisien untuk memantau kapasitas live dan mencegah double-booking |
| 🔐 **Secure Transaction Logging** | Audit trail ketat menggunakan Eloquent ORM, token masuk, dan validasi check-out |
| 🌐 **RESTful API Ready** | Endpoint API terstruktur, siap dikonsumsi oleh aplikasi Flutter, mobile, atau kontroler IoT |
| 📊 **Multi-Vehicle Tier Support** | Dukungan tarif berbeda untuk setiap kategori kendaraan secara fleksibel |
| 🏙️ **Smart City Ready** | Arsitektur modular yang mendukung integrasi sistem smart-city yang lebih luas |

---

## 🛠️ Dibangun Dengan

- **Framework:** [Laravel](https://laravel.com/) (PHP)
- **Database:** MySQL / PostgreSQL *(dengan indexing teroptimasi untuk pencarian transaksi cepat)*
- **Autentikasi:** Laravel Sanctum / Session Guards
- **Arsitektur:** Clean MVC dengan prinsip Separation of Concerns yang ketat

---

## 📋 Persyaratan Sistem

Sebelum memulai, pastikan lingkungan Anda memenuhi persyaratan berikut:

- **PHP** >= 8.1
- **Composer** >= 2.x
- **MySQL** >= 8.0 atau **PostgreSQL** >= 13
- **Node.js** >= 16.x *(untuk asset compilation)*

---

## 🔧 Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan environment development secara lokal:

### 1. Clone Repositori

```bash
git clone https://github.com/frisdiandi/ParkirApp.git
cd ParkirApp
```

### 2. Install Dependensi Composer

```bash
composer install
```

### 3. Konfigurasi Environment

Salin file environment dan buat application key yang unik:

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Buka file `.env`, sesuaikan kredensial database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parkirapp
DB_USERNAME=root
DB_PASSWORD=your_password
```

Kemudian jalankan migrasi dan seeding:

```bash
php artisan migrate --seed
```

### 5. Jalankan Aplikasi

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000` 🎉

---

## 📁 Struktur Proyek

```
ParkirApp/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Controller untuk setiap modul
│   │   └── Middleware/        # Middleware autentikasi & validasi
│   ├── Models/                # Eloquent Models
│   └── Services/              # Business logic & tariff engine
├── database/
│   ├── migrations/            # Skema database
│   └── seeders/               # Data awal & dummy data
├── routes/
│   ├── api.php                # RESTful API routes
│   └── web.php                # Web routes
└── tests/                     # Unit & Feature tests
```

---

## 🔌 API Endpoints

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/parking/check-in` | Proses kendaraan masuk |
| `POST` | `/api/parking/check-out` | Proses kendaraan keluar & hitung tarif |
| `GET`  | `/api/slots/availability` | Cek ketersediaan slot real-time |
| `GET`  | `/api/transactions` | Riwayat transaksi parkir |
| `GET`  | `/api/tariffs` | Daftar tarif per kategori kendaraan |

---

## 🤝 Kontribusi

Kontribusi, laporan isu, dan permintaan fitur sangat disambut! Berikut cara berkontribusi:

1. **Fork** repositori ini
2. Buat **branch** fitur baru (`git checkout -b feature/NamaFitur`)
3. **Commit** perubahan Anda (`git commit -m 'feat: Tambah fitur X'`)
4. **Push** ke branch (`git push origin feature/NamaFitur`)
5. Buka **Pull Request**

Silakan cek [halaman issues](https://github.com/frisdiandi/ParkirApp/issues) untuk melihat apa yang perlu dikerjakan.

---

## 📄 Lisensi

```
Copyright (c) 2024 Future Generation Technology

Perangkat lunak ini dilisensikan di bawah Lisensi Future Generation Technology (FGT).
Penggunaan, penyalinan, modifikasi, dan distribusi perangkat lunak ini
diperbolehkan untuk tujuan komersial maupun non-komersial dengan tetap
mencantumkan atribusi kepada Future Generation Technology.

Perangkat lunak ini disediakan "sebagaimana adanya" tanpa jaminan
apa pun, baik tersurat maupun tersirat.
```

**ParkirApp** adalah perangkat lunak open-source yang dilisensikan di bawah **[Future Generation Technology License](LICENSE)**.

---

<p align="center">
  Dibuat dengan ❤️ oleh <strong>Future Generation Technology</strong>
</p>
