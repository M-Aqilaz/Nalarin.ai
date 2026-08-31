![Nalarin.ai Logo](public/images/logo_nalarin_ai.png)

# Nalarin.ai

Nalarin.ai adalah platform pembelajaran berbasis AI yang membantu pengguna mengubah materi belajar menjadi pengalaman belajar yang lebih aktif, terstruktur, dan interaktif. Aplikasi ini menggabungkan ringkasan materi, Tutor AI, *flashcards*, kuis, *Pomodoro*, serta fitur sosial seperti obrolan grup ruang kelas dan *study matching*.

## Why Nalarin.ai

Belajar sering gagal bukan karena materi terlalu sulit, tetapi karena alurnya terputus-putus. Pengguna harus membaca, merangkum, mencari latihan, menjaga fokus, lalu mencari teman belajar secara terpisah. Nalarin.ai menyatukan alur tersebut ke dalam satu produk:

- memahami materi lebih cepat
- mengulang konsep penting dengan lebih terstruktur
- menjaga ritme belajar dengan tools fokus
- membuka ruang belajar sosial yang relevan

Dengan pendekatan ini, Nalarin.ai tidak hanya menjadi tools AI, tetapi menjadi ekosistem belajar yang lebih lengkap dan layak dikembangkan sebagai produk komersial.

## Visi Produk

Nalarin.ai dirancang untuk menjadi teman belajar digital yang tidak hanya membantu memahami materi, tetapi juga membantu pengguna menjaga ritme belajar, mengulang konsep penting, dan menemukan teman belajar baru.

## Fitur Utama

- Unggah materi belajar berbasis teks atau berkas
- Ringkasan otomatis dari materi
- Tutor AI khusus per materi
- *Flashcards* otomatis untuk ulasan cepat
- Kuis interaktif pilihan ganda
- *Pomodoro* untuk manajemen fokus belajar
- Obrolan grup ruang kelas untuk belajar bersama
- *Study matching* untuk menemukan rekan belajar
- Waktu nyata (*realtime*) obrolan, indikator pengetikan, dan pusat notifikasi
- Dashboard admin untuk pemantauan sistem

## Gambaran Pengalaman Pengguna

Alur utama pengguna dirancang sederhana:

1. Unggah materi atau tempel teks belajar
2. Baca ringkasan awal untuk memahami inti materi
3. Lanjutkan diskusi lewat Tutor AI
4. Ulangi materi dengan *flashcards* dan kuis
5. Gunakan *Pomodoro* untuk menjaga fokus
6. Masuk ke ruang kelas atau cari rekan belajar lewat *study matching*

Dengan pola ini, pengguna tidak hanya membaca materi, tetapi diarahkan ke siklus belajar yang lebih aktif.

## Stack Teknologi

- Laravel 13
- PHP 8.3
- Blade
- Tailwind CSS
- Vite
- MySQL
- Laravel Reverb
- Laravel Queue

## Struktur Inti

- `app/Http/Controllers`
  Logika utama aplikasi, termasuk fitur belajar, sosial, dan admin.
- `app/Models`
  Model domain seperti `Material`, `AiSummary`, `ChatThread`, `FlashcardDeck`, `QuizSet`, `StudyRoom`, dan `StudyMatch`.
- `app/Services/Learning`
  Service untuk generator konten belajar, ekstraksi teks materi, dan matching.
- `resources/views`
  Tampilan Blade untuk area publik, area user, dan area admin.
- `database/migrations`
  Definisi skema database.
- `database/seeders`
  Seeder untuk data demo dan pengujian.

## Preview Modul

- `Landing page`
  Menjelaskan value proposition, pricing, dan CTA produk.
- `Dashboard user`
  Merangkum progres belajar, kuota fitur, dan akses cepat ke modul utama.
- `AI learning tools`
  Ringkasan, tutor, *flashcards*, dan kuis dari materi yang diunggah.
- `Study management`
  *Pomodoro* untuk menjaga fokus sesi belajar.
- `Social learning`
  Obrolan grup ruang kelas dan *study matching*.
- `Admin panel`
  Monitoring statistik penggunaan, user, dan materi.

## Cara Menjalankan Project

1. Install dependency:

```bash
composer install
npm install
```

2. Siapkan environment:

```bash
cp .env.example .env
php artisan key:generate
```

3. Atur koneksi database pada file `.env`, lalu jalankan:

```bash
php artisan migrate
php artisan db:seed
```

4. Jalankan aplikasi:

```bash
php artisan serve
npm run dev
php artisan queue:work
php artisan reverb:start
```

`queue:work` diperlukan untuk notifikasi database dan balasan AI async. `reverb:start` diperlukan untuk realtime chat, typing indicator, dan event live lain.

## Docker

Project ini sudah punya setup Docker berbasis:

- `app` untuk PHP-FPM Laravel
- `web` untuk Nginx
- `db` untuk MySQL
- `queue` untuk worker background
- `reverb` untuk websocket realtime

Lihat detail lengkap di [DOCKER_DEPLOY.md](DOCKER_DEPLOY.md).

Setup ini butuh env tambahan untuk:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_PORT=8081
OPENAI_API_KEY=
OPENAI_BASE_URL=https://openrouter.ai/api/v1
OPENAI_MODEL=openai/gpt-oss-120b:free
```

Jika memakai social login atau domain production, callback OAuth dan host Reverb juga harus ikut diperbarui.

## Setup Production Singkat

Untuk deployment production, minimal siapkan:

- environment `.env` production yang benar
- `APP_ENV=production`
- `APP_DEBUG=false`
- database MySQL production
- storage yang dapat diakses untuk file upload
- proses build frontend:

```bash
npm run build
```

- cache konfigurasi Laravel:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- web server seperti Nginx atau Apache yang mengarah ke folder `public/`

## Akun Demo

Seeder demo menyiapkan beberapa akun untuk pengujian:

- `admin@nalarin.ai`
- `free@tester.com`
- `premium@tester.com`

Password default:

```text
password
```

## Catatan Pengembangan

- Fitur belajar utama sudah berjalan pada alur user login.
- Modul admin `users` dan `documents` sudah aktif.
- Halaman pomodoro masih memakai view mock yang memang dipakai langsung oleh route aktif.
- Realtime chat untuk room, study match, dan AI thread sudah aktif lewat Reverb.
- Beberapa bagian produk masih bersifat MVP dan bisa dikembangkan lebih lanjut, terutama pada presence, unread logic yang lebih kaya, dan event activity admin.

## Tujuan Berikutnya

- Menyempurnakan integrasi model AI nyata
- Menambahkan event/activity feed admin berbasis data real
- Menambahkan read receipts, presence online, dan moderation tooling yang lebih kaya
- Memperkuat audit, test coverage, dan readiness production

## Roadmap Singkat

- `Phase 1`
  Menstabilkan fondasi fitur belajar dan admin.
- `Phase 2`
  Memperkuat fitur sosial, realtime chat, dan quality of life pengguna.
- `Phase 3`
  Menambahkan monetisasi yang lebih matang, analitik, dan kesiapan production penuh.

## License

Project ini dikembangkan untuk kebutuhan internal/pengembangan produk Nalarin.ai.
