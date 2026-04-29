# Docker Deploy

Setup ini dibuat untuk deployment Laravel production dengan:

- `php-fpm`
- `nginx`
- `mysql`
- `queue worker`
- `reverb websocket server`
- OCR lokal dengan Tesseract dan Poppler
- `Docker multi-stage build`

Arsitektur ini mengikuti praktik umum dari dokumentasi resmi Docker untuk `multi-stage builds`, serta memakai official images untuk `php` dan `nginx`.

## File Yang Ditambahkan

- [Dockerfile](/C:/laragon/www/nalarin_ai/Pelajarin.ai/Dockerfile)
- [compose.yaml](/C:/laragon/www/nalarin_ai/Pelajarin.ai/compose.yaml)
- [.dockerignore](/C:/laragon/www/nalarin_ai/Pelajarin.ai/.dockerignore)
- [.env.docker.example](/C:/laragon/www/nalarin_ai/Pelajarin.ai/.env.docker.example)
- [docker/nginx/default.conf](/C:/laragon/www/nalarin_ai/Pelajarin.ai/docker/nginx/default.conf)
- [docker/php/app.ini](/C:/laragon/www/nalarin_ai/Pelajarin.ai/docker/php/app.ini)
- [docker/php/opcache.ini](/C:/laragon/www/nalarin_ai/Pelajarin.ai/docker/php/opcache.ini)
- [docker/php/entrypoint.sh](/C:/laragon/www/nalarin_ai/Pelajarin.ai/docker/php/entrypoint.sh)

## Cara Pakai

### 1. Siapkan env Docker

Copy:

```bash
cp .env.docker.example .env
```

Di Windows PowerShell:

```powershell
Copy-Item .env.docker.example .env
```

Lalu isi:

- `APP_KEY`
- database password
- `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
- `OPENAI_API_KEY`
- `OCR_LANGUAGES` jika ingin bahasa OCR selain default `ind+eng`
- Google login
- Discord login

Generate app key kalau belum ada:

```bash
docker compose run --rm app php artisan key:generate --show
```

Lalu copy hasilnya ke `APP_KEY=` di file `.env`.

### 2. Build Dan Jalankan

```bash
docker compose up -d --build
```

### 3. Jalankan Migration

```bash
docker compose run --rm app php artisan migrate --force
```

Kalau butuh seeder:

```bash
docker compose run --rm app php artisan db:seed --force
```

### 4. Realtime Dan Worker

Setup ini sekarang butuh 2 proses background aktif:

- `queue`
  untuk job AI reply dan database notifications
- `reverb`
  untuk realtime chat, typing indicator, dan push event

Keduanya sudah dinyalakan otomatis lewat `docker compose up -d`.

Default port:

- aplikasi web: `http://localhost:8080`
- websocket Reverb: `ws://localhost:8081`

### 5. Buat Storage Link

Entrypoint container sudah menyiapkan link `public/storage` otomatis.

### 6. Akses Aplikasi

Default:

```text
http://localhost:8080
```

Kalau ingin port lain:

```env
APP_PORT=80
```

atau port lain sesuai kebutuhan.

Catatan:

- `APP_PORT` default `8080`
- `REVERB_PORT` default `8081`
- jangan samakan dua port host ini

## Service Yang Disediakan

### `app`

- menjalankan `php-fpm`
- mengeksekusi Laravel
- berisi dependency OCR:
  - `tesseract-ocr`
  - `tesseract-ocr-ind`
  - `tesseract-ocr-eng`
  - `poppler-utils`

### `web`

- menjalankan `nginx`
- menerima request HTTP
- meneruskan file PHP ke `app`

### `queue`

- menjalankan `php artisan queue:work`
- wajib untuk:
  - AI reply async
  - database notifications
  - job lain berbasis queue

### `reverb`

- menjalankan `php artisan reverb:start`
- melayani websocket untuk:
  - private chat
  - room chat
  - study match
  - typing indicator
  - status AI thread

### `db`

- menjalankan `mysql:8.4`
- menyimpan data aplikasi

## Catatan Penting

- setup ini cocok untuk VPS atau hosting yang mendukung Docker Compose
- untuk production nyata, set `APP_ENV=production` dan `APP_DEBUG=false`
- untuk domain production, ubah `APP_URL`, `GOOGLE_REDIRECT_URI`, `DISCORD_REDIRECT_URI`, `REVERB_HOST`, dan `VITE_REVERB_HOST`
- build frontend Vite sudah dilakukan di stage `node`
- image akhir diperkecil dengan `multi-stage build`
- jika memakai domain production, `REVERB_SCHEME` dan `VITE_REVERB_SCHEME` biasanya harus `https`
- `BROADCAST_CONNECTION` harus tetap `reverb`
- `QUEUE_CONNECTION` harus tetap `database` atau diganti konsisten ke backend queue lain
- `OPENAI_BASE_URL` default mengarah ke OpenRouter: `https://openrouter.ai/api/v1`
- `OPENAI_MODEL` default memakai `openai/gpt-oss-120b:free`
- tanpa `OPENAI_API_KEY`, AI tutor realtime tetap hidup tetapi balasan AI akan gagal dan status thread menjadi `failed`
- OCR PDF scan dan gambar memakai Tesseract; PDF ke gambar memakai Poppler
- file Office modern `.docx`, `.pptx`, dan `.xlsx` dibaca langsung tanpa LibreOffice
- file Office lama `.doc`, `.ppt`, dan `.xls` tidak diproses otomatis di mode ringan; convert ke PDF/DOCX/PPTX/XLSX sebelum upload
- akun free default dibatasi OCR sampai `OCR_FREE_MAX_PAGES=5` halaman per PDF, sedangkan premium default `OCR_PREMIUM_MAX_PAGES=50`

## Command Berguna

Lihat log:

```bash
docker compose logs -f
```

Lihat log service tertentu:

```bash
docker compose logs -f app
docker compose logs -f web
docker compose logs -f queue
docker compose logs -f reverb
docker compose logs -f db
```

Masuk ke container app:

```bash
docker compose exec app sh
```

Restart:

```bash
docker compose restart
```

Stop:

```bash
docker compose down
```

Stop dan hapus volume database:

```bash
docker compose down -v
```

## Sumber

- Docker multi-stage builds: https://docs.docker.com/build/building/multi-stage/
- PHP official image: https://hub.docker.com/_/php/
- Nginx official image: https://hub.docker.com/_/nginx
- Laravel Reverb: https://laravel.com/docs/reverb
- Tesseract OCR: https://tesseractocr.org/
- OCRmyPDF/konsep OCR PDF: https://ocrmypdf.readthedocs.io/
