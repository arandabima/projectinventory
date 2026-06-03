# Aplikasi Inventory Laravel

Aplikasi inventory berbasis Laravel untuk tiga service bisnis:

- Pencatatan stok dan barang
- Cetak laporan CSV
- Notif dan komunikasi internal

Stack:

- Laravel
- PostgreSQL Neon.tech
- Docker container
- Docker Compose + Traefik

## Menjalankan

1. Nyalakan Docker Desktop.
2. Salin `.env.example` menjadi `.env`.
3. Isi kredensial database Neon PostgreSQL.
4. Jalankan:

```powershell
docker compose up --build
```

5. Buka:

- Aplikasi: http://inventory.localhost
- Dashboard Traefik: http://localhost:8080

Jika port 80 sudah dipakai aplikasi lain, hentikan service tersebut dulu atau ubah mapping port Traefik pada `docker-compose.yml`.

## Service

Docker Compose menjalankan Traefik dan tiga service aplikasi Laravel:

- `pencatatan`: route utama dan modul pencatatan barang/mutasi stok
- `cetak-laporan`: route `/laporan` untuk export CSV
- `notif-komunikasi`: route `/notifikasi` untuk pesan dan notifikasi stok

Ketiga service memakai image Laravel yang sama, tetapi dipisahkan di level container dan routing Traefik agar modul bisnisnya jelas.

## Database Neon

Gunakan koneksi PostgreSQL Neon dengan `DB_SSLMODE=require`.
Contoh variabel tersedia di `.env.example`.

Contoh format host Neon biasanya seperti `ep-nama-project.region.aws.neon.tech`. Isi `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` sesuai detail connection string dari dashboard Neon.

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