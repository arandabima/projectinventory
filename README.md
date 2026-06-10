# Aplikasi Inventory Laravel

Aplikasi inventory berbasis Laravel untuk tiga service bisnis:

- Pencatatan stok dan barang
- Cetak laporan CSV
- Notif dan komunikasi internal

Stack:

- Laravel
- PostgreSQL Neon.tech
- Docker container
- Docker Compose
- Cloudinary untuk upload media (gambar, video, dokumen)
- DOKU untuk payment gateway

## Menjalankan

1. Nyalakan Docker Desktop.
2. Salin `.env.example` menjadi `.env`.
3. Isi kredensial database Neon PostgreSQL.
4. Isi kredensial Cloudinary:
   - `CLOUDINARY_CLOUD_NAME`: nama cloud Cloudinary Anda
   - `CLOUDINARY_API_KEY`: API key dari dashboard Cloudinary
   - `CLOUDINARY_API_SECRET`: API secret dari dashboard Cloudinary
5. Isi kredensial DOKU Payment:
   - `DOKU_MALL_ID`: Mall ID dari dashboard DOKU
   - `DOKU_SHARED_KEY`: Shared Key dari dashboard DOKU
   - `DOKU_SANDBOX_URL`: URL sandbox DOKU
   - `DOKU_NOTIFICATION_URL`: URL webhook untuk notifikasi pembayaran
6. Jalankan:

```powershell
docker compose up --build
```

7. Buka:

- Aplikasi utama: http://localhost
- Cetak laporan: http://localhost/laporan
- Notifikasi: http://localhost/notifikasi

Aplikasi berjalan di port 80 HTTP standar dan di-route melalui Nginx reverse proxy ke ketiga services.

## Service & Routing

Aplikasi dijalankan melalui Nginx reverse proxy di port 80 yang mendistribusikan request ke tiga Laravel services:

- `pencatatan`: service utama - route `/` untuk pencatatan stok dan mutasi barang
- `cetak-laporan`: route `/laporan` untuk export laporan CSV
- `notif-komunikasi`: route `/notifikasi` untuk pesan dan notifikasi stok

Ketiga service menggunakan image Laravel yang sama tetapi dipisahkan per container untuk memisahkan business logic setiap modul.

Nginx menghandle reverse proxy dengan routing berbasis path (location blocks).

## Database Neon

Gunakan koneksi PostgreSQL Neon dengan `DB_SSLMODE=require`.
Contoh variabel tersedia di `.env.example`.

Contoh format host Neon biasanya seperti `ep-nama-project.region.aws.neon.tech`. Isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai detail connection string dari dashboard Neon.

## Cloudinary Setup

Aplikasi menggunakan Cloudinary untuk mengelola upload media (gambar, video, dokumen).

1. Daftar akun di [Cloudinary](https://cloudinary.com/)
2. Dari dashboard Cloudinary, dapatkan:
   - Cloud Name (di bagian "Account Details")
   - API Key dan API Secret (di bagian "API Keys")
3. Isi nilai-nilai tersebut di `.env`:
   ```
   CLOUDINARY_CLOUD_NAME=your-cloud-name
   CLOUDINARY_API_KEY=your-api-key
   CLOUDINARY_API_SECRET=your-api-secret
   ```

## DOKU Payment Setup

Aplikasi menggunakan DOKU sebagai payment gateway untuk memproses pembayaran.

1. Daftar atau login ke dashboard [DOKU](https://doku.com/)
2. Dapatkan kredensial dari dashboard:
   - Mall ID: ID merchant Anda
   - Shared Key: Key rahasia untuk signing request
   - Sandbox URL: URL environment sandbox untuk testing
3. Isi nilai-nilai tersebut di `.env`:
   ```
   DOKU_MALL_ID=your-mall-id
   DOKU_SHARED_KEY=your-shared-key
   DOKU_SANDBOX_URL=https://sandbox.doku.com
   DOKU_NOTIFICATION_URL=https://your-app-url/webhook/doku
   ```
4. Update `DOKU_NOTIFICATION_URL` dengan URL webhook aplikasi Anda untuk menerima notifikasi pembayaran dari DOKU.

## Migrasi dan Seeder

Jika `RUN_MIGRATIONS=true`, service `pencatatan` menjalankan migrasi otomatis saat start.
Untuk seed data contoh:

```powershell
docker compose exec pencatatan php artisan db:seed
```

Perintah ringkasan inventory:

```powershell
docker compose exec pencatatan php artisan inventory:summary
```