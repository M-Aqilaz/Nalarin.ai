# Deploy Checklist

Checklist ini dipakai saat `Nalarin.ai` dipindahkan dari local ke domain production.

## 1. Domain Dan Server

- domain utama sudah aktif
- DNS domain sudah mengarah ke server
- web server mengarah ke folder `public`
- SSL / HTTPS sudah aktif
- akses `https://domain-anda.com` sudah terbuka

## 2. Environment File

Siapkan `.env` production.

Yang wajib dicek:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
```

Database:

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

Session dan app:

```env
SESSION_DRIVER=database
SESSION_DOMAIN=null
QUEUE_CONNECTION=database
CACHE_STORE=database
```

Kalau nanti memakai subdomain, opsional:

```env
SESSION_DOMAIN=.domain-anda.com
```

## 3. App Key

Pastikan `APP_KEY` ada.

Kalau belum ada:

```bash
php artisan key:generate
```

## 4. Database

Jalankan migration:

```bash
php artisan migrate --force
```

Kalau butuh data awal:

```bash
php artisan db:seed --force
```

Catatan:

- jangan seed data dummy di production kalau tidak diperlukan

## 5. Storage

Kalau file upload dipakai, pastikan symbolic link storage aktif:

```bash
php artisan storage:link
```

## 6. Cache Laravel

Setelah `.env` final:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Lalu cache ulang:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 7. Frontend Assets

Build assets production:

```bash
npm install
npm run build
```

Pastikan hasil build Vite ada dan bisa diakses.

## 8. Queue

Karena project memakai `QUEUE_CONNECTION=database`, pastikan worker jalan:

```bash
php artisan queue:work
```

Untuk production, worker sebaiknya dijalankan lewat process manager seperti:

- Supervisor
- systemd
- pm2 jika perlu wrapper

## 9. Social Login

### Google

Di `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://domain-anda.com/auth/google/callback
```

Di Google Cloud Console:

- update `Authorized redirect URI`
- pastikan sama persis:

```text
https://domain-anda.com/auth/google/callback
```

### Discord

Di `.env`:

```env
DISCORD_CLIENT_ID=
DISCORD_CLIENT_SECRET=
DISCORD_REDIRECT_URI=https://domain-anda.com/auth/discord/callback
```

Di Discord Developer Portal:

- update `Redirects`
- pastikan sama persis:

```text
https://domain-anda.com/auth/discord/callback
```

## 10. Security Basic

- `APP_DEBUG=false`
- pakai HTTPS
- secret di `.env` production tidak dibagikan
- database credential production aman
- admin account memakai password kuat
- akun test/dummy dibersihkan bila tidak dibutuhkan

## 11. Test Setelah Deploy

Test manual minimal:

- homepage terbuka
- register
- login email/password
- login Google
- login Discord
- upload material
- summary
- chat
- flashcards
- quiz
- pomodoro
- rooms
- matchmaking
- admin dashboard

## 12. Cek Error Log

Setelah deploy, cek:

- log Laravel di `storage/logs`
- error web server
- queue worker log

## 13. Final Checklist

- domain aktif
- HTTPS aktif
- `.env` production benar
- migration sukses
- storage link aktif
- assets production sudah dibuild
- queue worker berjalan
- Google login jalan
- Discord login jalan
- admin dashboard jalan
- fitur inti user jalan
