# Social Login Setup

Dokumen ini menjelaskan social login `Google` dan `Discord` untuk project `Nalarin.ai`.

## Tool Yang Dipakai

Project ini tidak memakai Firebase.

Tool yang dipakai:

- `laravel/socialite`
- `socialiteproviders/manager`
- `socialiteproviders/discord`
- OAuth provider dari `Google`
- OAuth provider dari `Discord`

Alasan memakai setup ini:

- cocok untuk Laravel + Blade + session auth
- tetap sederhana untuk web app
- tidak perlu database baru
- cukup memakai kolom tambahan di tabel `users`

## Implementasi Di Project Ini

Bagian yang sudah dipasang:

- package: `laravel/socialite`
- package: `socialiteproviders/manager`
- package: `socialiteproviders/discord`
- controller: [app/Http/Controllers/Auth/SocialAuthController.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/app/Http/Controllers/Auth/SocialAuthController.php)
- provider bootstrap: [bootstrap/providers.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/bootstrap/providers.php)
- provider listener: [app/Providers/AppServiceProvider.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/app/Providers/AppServiceProvider.php)
- route auth: [routes/auth.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/routes/auth.php)
- config services: [config/services.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/config/services.php)
- login page: [resources/views/auth/login.blade.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/resources/views/auth/login.blade.php)
- users table update: [database/migrations/2026_04_21_100000_add_social_login_fields_to_users_table.php](/C:/laragon/www/nalarin_ai/Pelajarin.ai/database/migrations/2026_04_21_100000_add_social_login_fields_to_users_table.php)

Kolom tambahan di `users`:

- `provider`
- `provider_id`
- `provider_avatar`

## Route Yang Dipakai

Google:

- `GET /auth/google/redirect`
- `GET /auth/google/callback`

Discord:

- `GET /auth/discord/redirect`
- `GET /auth/discord/callback`

## Konfigurasi .env

Tambahkan ke file `.env`:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

DISCORD_CLIENT_ID=
DISCORD_CLIENT_SECRET=
DISCORD_REDIRECT_URI=http://127.0.0.1:8000/auth/discord/callback
DISCORD_AVATAR_GIF=true
DISCORD_EXTENSION_DEFAULT=png
```

Setelah mengubah `.env`, jalankan:

```bash
php artisan config:clear
```

## Jika Nanti Pakai Domain .com

Kalau project sudah online, misalnya memakai domain:

```text
https://nalarin.ai
```

Maka yang perlu diubah:

### 1. Ubah `.env`

Contoh:

```env
APP_URL=https://nalarin.ai

GOOGLE_REDIRECT_URI=https://nalarin.ai/auth/google/callback

DISCORD_CLIENT_ID=
DISCORD_CLIENT_SECRET=
DISCORD_REDIRECT_URI=https://nalarin.ai/auth/discord/callback
```

Kalau login dan session dipakai lintas subdomain, opsional:

```env
SESSION_DOMAIN=.nalarin.ai
```

Kalau hanya satu domain utama, `SESSION_DOMAIN` bisa tetap `null`.

### 2. Update Google OAuth

Di Google Cloud Console:

- ganti `Authorized redirect URI` ke:

```text
https://nalarin.ai/auth/google/callback
```

- opsional `Authorized JavaScript origins`:

```text
https://nalarin.ai
```

### 3. Update Discord OAuth2

Di Discord Developer Portal:

- ganti `Redirects` ke:

```text
https://nalarin.ai/auth/discord/callback
```

### 4. Pastikan HTTPS Aktif

Untuk domain production, sangat disarankan:

- memakai `https`
- SSL certificate aktif
- jangan memakai callback `http`

### 5. Refresh Config Laravel

Setelah ubah `.env`, jalankan:

```bash
php artisan config:clear
```

Kalau server production memakai cache config:

```bash
php artisan config:cache
```

### 6. Test Ulang Login

Test ulang:

- `https://nalarin.ai/login`
- login Google
- login Discord

### Checklist Saat Ganti Domain

- `APP_URL` sudah domain production
- `GOOGLE_REDIRECT_URI` sudah domain production
- `DISCORD_REDIRECT_URI` sudah domain production
- callback di Google sudah diganti
- callback di Discord sudah diganti
- HTTPS sudah aktif
- config Laravel sudah di-clear / cache ulang

## Konfigurasi Google

Langkah ringkas:

1. buka `Google Cloud Console`
2. buat atau pilih project
3. buat `OAuth Client`
4. pilih tipe `Web application`
5. isi redirect URI:

```text
http://127.0.0.1:8000/auth/google/callback
```

6. salin `Client ID` dan `Client Secret`
7. tempel ke `.env`

Opsional origin lokal:

```text
http://127.0.0.1:8000
```

Catatan penting:

- redirect URI harus sama persis
- kalau beda sedikit saja, Google akan memberi error `redirect_uri_mismatch`

## Konfigurasi Discord

Langkah ringkas:

1. buka `Discord Developer Portal`
2. buat aplikasi baru
3. buka menu `OAuth2`
4. tambahkan redirect:

```text
http://127.0.0.1:8000/auth/discord/callback
```

5. ambil:
   - `Client ID`
   - `Client Secret`
6. tempel ke `.env`

Scope yang dipakai di project ini:

- `identify`
- `email`

Catatan penting:

- redirect URI Discord harus sama persis dengan `.env`
- tanpa scope `email`, Discord bisa tidak mengembalikan email user

## Cara Kerja Login Di Project

Alur social login di project ini:

1. user klik tombol `Google` atau `Discord`
2. user diarahkan ke provider OAuth
3. provider mengembalikan data user ke callback
4. sistem mencari user berdasarkan:
   - `provider + provider_id`, atau
   - `email` jika akun sudah ada
5. jika user belum ada, sistem membuat akun baru
6. sistem menyimpan:
   - `provider`
   - `provider_id`
   - `provider_avatar`
7. user otomatis login ke aplikasi

## Aturan Akun

Project ini tetap memakai aturan user internal:

- kalau `is_active = false`, akun akan ditolak
- user admin akan diarahkan ke `admin dashboard`
- user biasa akan diarahkan ke `dashboard`

## Test Login Google

Urutan test:

1. isi `.env`
2. jalankan:

```bash
php artisan config:clear
```

3. buka:

```text
http://127.0.0.1:8000/login
```

4. klik `Lanjutkan dengan Google`

Kalau berhasil:

- akun dibuat atau dihubungkan ke user yang sudah ada
- user masuk ke dashboard

## Test Login Discord

Urutan test:

1. isi `.env`
2. jalankan:

```bash
php artisan config:clear
```

3. buka:

```text
http://127.0.0.1:8000/login
```

4. klik `Lanjutkan dengan Discord`

Kalau berhasil:

- akun dibuat atau dihubungkan ke user yang sudah ada
- user masuk ke dashboard

## Troubleshooting

`Google: redirect_uri_mismatch`

- cek `GOOGLE_REDIRECT_URI`
- cek redirect URI di Google Console
- harus sama persis

`Discord: invalid redirect_uri`

- cek `DISCORD_REDIRECT_URI`
- cek redirect yang didaftarkan di Discord Developer Portal
- harus sama persis

`Tombol social login tidak aktif`

- berarti credential provider belum lengkap di `.env`
- cek `CLIENT_ID`, `CLIENT_SECRET`, dan `REDIRECT_URI`

`Akun Anda sedang dinonaktifkan`

- user cocok dengan akun yang `is_active = false`
- aktifkan lagi dari admin atau database

## Ringkasan

Untuk project ini:

- `pakai`: `Laravel Socialite`
- `pakai`: `SocialiteProviders Discord`
- `tidak pakai`: `Firebase`
- `database baru`: tidak perlu
- `tabel baru`: tidak perlu
- `cukup`: tambah kolom social login di `users`
