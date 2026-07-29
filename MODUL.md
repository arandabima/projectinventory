# Sistem Integrasi — Ruang Lingkup Implementasi

Proyek mengikuti Modul 2 Sistem Integrasi untuk empat bagian berikut:

1. **Authentication** — login username/password dan Google OAuth melalui Laravel Socialite.
2. **Inventory** — admin mengelola produk, stok, harga, kategori, status, dan gambar produk di Cloudinary.
3. **E-Commerce** — pengguna melihat katalog di `/shop/products`, mengelola cart, lalu checkout.
4. **Payment Gateway** — checkout membuat request DOKU Sandbox. Webhook DOKU tervalidasi mengubah payment serta order menjadi `paid` dan mengurangi stok secara atomik.

Bagian yang tidak termasuk scope: Virtual Account, Shipping/Logistic API, CRM, database customer terpisah, dan Object Storage/MinIO.

## Menjalankan dengan Docker

1. Salin `.env.example` ke `.env`, isi `APP_KEY`, kredensial Google OAuth, Neon PostgreSQL, Cloudinary, serta DOKU Sandbox.
2. Jalankan `docker compose up --build`.
3. Jalankan migrasi: `docker compose exec app php artisan migrate`.
4. Aplikasi tersedia di `http://localhost`. Database memakai Neon PostgreSQL, bukan container lokal.

## Webhook DOKU Sandbox

Atur Notification URL pada dashboard DOKU Sandbox ke `<URL-publik>/webhook/doku`. Untuk development lokal, URL tersebut perlu diekspos dengan tunnel HTTPS.
