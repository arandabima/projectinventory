# Project Inventory Laravel

Aplikasi inventory berbasis Laravel yang sudah dikembangkan dan disesuaikan untuk menangani alur bisnis inventory, e-commerce, pembayaran, serta komunikasi internal. Proyek ini merupakan hasil pengembangan menyeluruh yang mencakup integrasi berbagai modul menjadi satu sistem yang runtut dan siap digunakan.

## Ringkasan Pekerjaan yang Sudah Selesai

Berikut adalah poin-poin utama yang telah diselesaikan dalam proyek ini:

- Implementasi sistem autentikasi dan manajemen pengguna.
  - Login menggunakan username/password.
  - Integrasi login Google melalui Laravel Socialite.
  - Pengaturan user dan akses dasar untuk mendukung alur aplikasi.

- Pengembangan modul inventory.
  - Pengelolaan barang, kategori, status, stok, dan harga.
  - Fitur pencatatan mutasi stok dan pemantauan ketersediaan barang.
  - Upload gambar produk melalui Cloudinary.

- Pengembangan modul e-commerce.
  - Halaman katalog produk untuk pengguna.
  - Fitur keranjang belanja dan checkout.
  - Proses pembuatan order dan detail order.

- Integrasi pembayaran.
  - Integrasi gateway pembayaran DOKU Sandbox.
  - Proses pembayaran yang menghasilkan status order dan payment yang terkelola.
  - Webhook DOKU untuk menerima notifikasi pembayaran secara otomatis.

- Fitur notifikasi dan komunikasi.
  - Sistem notifikasi terkait proses bisnis.
  - Komunikasi internal untuk mendukung alur kerja aplikasi.

- Penyediaan lingkungan deployment dan pengembangan.
  - Konfigurasi Docker dan Docker Compose.
  - Reverse proxy menggunakan Nginx.
  - Dukungan koneksi database PostgreSQL Neon.

## Teknologi yang Digunakan

- Laravel
- PostgreSQL Neon
- Docker / Docker Compose
- Nginx
- Cloudinary untuk media upload
- DOKU Payment Gateway

## Struktur Aplikasi

Aplikasi ini dirancang untuk menjalankan beberapa kebutuhan bisnis dalam satu ekosistem:

- Modul inventory untuk pengelolaan barang dan stok.
- Modul e-commerce untuk transaksi pelanggan.
- Modul pembayaran untuk memproses transaksi.
- Modul komunikasi untuk notifikasi dan interaksi internal.

## Cara Menjalankan Aplikasi

1. Pastikan Docker Desktop sudah berjalan.
2. Salin file `.env.example` menjadi `.env`.
3. Isi konfigurasi environment yang diperlukan, seperti:
   - Database PostgreSQL Neon
   - Cloudinary
   - DOKU
   - Google OAuth
4. Jalankan perintah berikut:

```powershell
docker compose up --build
```

5. Jalankan migrasi database:

```powershell
docker compose exec app php artisan migrate
```

6. Jika ingin mengisi data awal, jalankan seeder:

```powershell
docker compose exec app php artisan db:seed
```

7. Buka aplikasi melalui browser di:

- http://localhost

## Konfigurasi Penting

### Database Neon

Gunakan koneksi PostgreSQL Neon dengan konfigurasi yang sesuai, termasuk SSL mode yang dibutuhkan oleh layanan Neon.

### Cloudinary

Isi variabel berikut di file `.env`:

```env
CLOUDINARY_CLOUD_NAME=your-cloud-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
```

### DOKU Payment

Isi kredensial DOKU Sandbox pada file `.env`:

```env
DOKU_MALL_ID=your-mall-id
DOKU_SHARED_KEY=your-shared-key
DOKU_SANDBOX_URL=https://sandbox.doku.com
DOKU_NOTIFICATION_URL=https://your-app-url/webhook/doku
```

> Untuk webhook DOKU, URL publik yang bisa diakses internet sangat dibutuhkan agar notifikasi pembayaran dapat diterima oleh aplikasi.

## Catatan Akhir

Proyek ini sudah melalui tahapan pengembangan utama dan integrasi fitur yang dibutuhkan untuk menjalankan sistem inventory modern dengan dukungan pembayaran dan notifikasi. README ini dibuat sebagai dokumentasi ringkas mengenai apa saja yang sudah dikerjakan dan bagaimana menjalankan aplikasi secara lokal.