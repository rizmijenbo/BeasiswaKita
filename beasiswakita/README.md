# BeasiswaKita

**Pusat Informasi Kesempatan Pendidikan** — *"Temukan Peluang, Raih Masa Depan."*

Website katalog beasiswa untuk siswa SMP, SMA/sederajat, calon mahasiswa, dan mahasiswa.
Dibangun dengan **Laravel (PHP 8.3)**, **Blade + Bootstrap 5.3 + Bootstrap Icons (CDN)**, dan **MySQL 8.0**,
dijalankan lewat **Docker Compose** (`app` PHP-FPM, `webserver` Nginx, `db` MySQL, `phpmyadmin`).

---

## Menjalankan dari awal (Docker Compose)

Prasyarat: Docker + Docker Compose v2. Jalankan dari folder proyek.

```bash
# 1. (Linux/macOS) samakan UID/GID di .env agar file tidak berpemilik root
#    -> ubah DOCKER_UID dan DOCKER_GID sesuai hasil:  id -u  &&  id -g
#    (Windows/Docker Desktop: lewati langkah ini)

# 2. Pastikan file .env ada (sudah disertakan; kalau belum:)
cp .env.example .env

# 3. Build image & jalankan semua service
docker compose up -d --build

# 4. Pasang dependensi Laravel
docker compose exec -u www-data app composer install

# 5. Buat APP_KEY
docker compose exec -u www-data app php artisan key:generate

# 6. Buat tabel + isi data demo
docker compose exec -u www-data app php artisan migrate --seed
```

| Layanan | URL |
|---|---|
| Website | http://localhost:8000 |
| phpMyAdmin | http://localhost:8080 (user `beasiswa`, password `secret`; atau `root` / `rootsecret`) |
| MySQL dari host | `127.0.0.1:3307` |

Port & kredensial bisa diubah di `.env` (`APP_PORT`, `PMA_PORT`, `DB_FORWARD_PORT`, `DB_*`).

### Perintah berguna

```bash
docker compose logs -f app                              # log PHP-FPM
docker compose exec -u www-data app php artisan test     # jalankan test
docker compose exec -u www-data app php artisan db:seed  # segarkan data demo (idempotent)
docker compose exec -u www-data app php artisan migrate:fresh --seed   # reset database
docker compose exec -u www-data app php artisan reminders:send         # kirim email pengingat sekarang
docker compose down            # hentikan (data DB tetap ada)
docker compose down -v         # hentikan + hapus data DB
```

---

## Fitur → lokasi kode

| Fitur | Lokasi |
|---|---|
| Pencarian (nama/penyelenggara) | `Scholarship::scopeSearch`, `ScholarshipController@index`, auto-submit JS di `scholarships/index.blade.php` |
| Filter jenjang (Semua/SMP/SMA/Kuliah/Umum) & status (Buka/Segera Tutup) | `Scholarship::scopeLevel/openNow/closingSoon`, `ScholarshipController@index` |
| Persyaratan terstruktur (Akademik/Administrasi/Umum) | tabel `requirements`, `Requirement::CATEGORIES`, `scholarships/show.blade.php` |
| Kalender pendaftaran (grid bulanan + agenda) | `ScholarshipController@calendar`, `calendar.blade.php` (`/kalender?bulan=YYYY-MM`) |
| Countdown & badge (merah jika ≤ 14 hari) | accessor di `Scholarship` (`days_left`, `status`, `countdown_label`, `badge_class`) |
| Pengingat email | `StoreReminderRequest`, `ScholarshipController@storeReminder`, tabel `reminders`, command `reminders:send` |
| Panduan dokumen + checklist LocalStorage | tabel `document_guides`, `guide.blade.php` |

### Halaman

| URL | Halaman |
|---|---|
| `/` | Beranda (hero, search, filter cepat, statistik, "Beasiswa Segera Tutup") |
| `/beasiswa` | Daftar beasiswa (`?q=`, `?jenjang=`, `?status=buka\|segera`) |
| `/beasiswa/{slug}` | Detail beasiswa |
| `/kalender` | Kalender pendaftaran |
| `/panduan-dokumen` | Panduan dokumen + checklist |

### Skema database

- `scholarships` — nama, slug, penyelenggara, jenjang, ringkasan, deskripsi, `benefits` (JSON), `open_date`, `deadline`, `official_url`
- `requirements` — `scholarship_id`, `category` (`akademik|administrasi|umum`), deskripsi, urutan
- `document_guides` — slug, judul, ikon, ringkasan, `steps` (JSON), `tips` (JSON)
- `document_guide_scholarship` — pivot: dokumen wajib tiap beasiswa
- `reminders` — `scholarship_id`, `email`, `days_before`, `notified_at` (unik per beasiswa+email)

---

## Catatan penting

**Data demo.** Program dalam seeder adalah beasiswa nyata di Indonesia (PIP SMP, PIP SMA/SMK, KIP Kuliah, Djarum Beasiswa Plus,
Karya Salemba Empat, LPDP Reguler, Tanoto Foundation), tetapi:

- Persyaratan ditulis **ringkas dan umum**, bukan salinan ketentuan resmi. Ketentuan berubah tiap periode.
  Verifikasi setiap beasiswa (termasuk URL resmi) sebelum website dipublikasikan.
- **Tanggal buka/tutup dibuat relatif terhadap hari seeding** (bukan jadwal resmi) supaya countdown dan kalender selalu terlihat hidup.
  Jalankan `php artisan db:seed` lagi untuk menyegarkan tanggal demo.
- Belum ada contoh jenjang **Umum** pada data demo (filternya tersedia dan menampilkan status kosong yang ramah).

**Email pengingat.** Secara default `MAIL_MAILER=log`: email ditulis ke `storage/logs/laravel.log`.
Isi kredensial SMTP di `.env` untuk pengiriman sungguhan. Pengiriman otomatis dijadwalkan harian pukul 07:00
(`routes/console.php`); aktifkan dengan menjalankan `php artisan schedule:work` (atau cron `schedule:run` tiap menit di server).

**Produksi.** Konfigurasi ini untuk development. Sebelum produksi: `APP_ENV=production`, `APP_DEBUG=false`,
kata sandi database yang kuat, tutup phpMyAdmin & port MySQL, dan jalankan `php artisan config:cache route:cache view:cache`.

## Troubleshooting

- **`Permission denied` pada `storage/` atau `bootstrap/cache/` (Linux):** pastikan `DOCKER_UID`/`DOCKER_GID` di `.env` sama dengan `id -u`/`id -g`,
  lalu `docker compose up -d --build`.
- **Port bentrok:** ubah `APP_PORT`, `PMA_PORT`, atau `DB_FORWARD_PORT` di `.env`.
- **Error `No application encryption key`:** jalankan langkah 5 (`key:generate`).
- **`SQLSTATE[HY000] [2002]` saat migrate:** tunggu MySQL sehat (`docker compose ps`), lalu ulangi.
