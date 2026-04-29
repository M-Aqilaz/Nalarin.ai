# Session Changes

Timestamp: `2026-04-20 15:34:15`

Dokumen ini merangkum semua file yang berubah selama percakapan ini berdasarkan working tree saat ini.

## Modified Files

- `app/Http/Controllers/AdminController.php`
- `app/Http/Controllers/Learning/ChatMessageController.php`
- `app/Http/Controllers/Learning/ChatThreadController.php`
- `app/Http/Controllers/Learning/MaterialController.php`
- `app/Http/Controllers/Learning/SummaryController.php`
- `app/Models/ChatThread.php`
- `app/Models/Material.php`
- `app/Models/User.php`
- `bootstrap/app.php`
- `database/seeders/DatabaseSeeder.php`
- `resources/views/Admin/adminDashboard.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/welcome.blade.php`
- `routes/web.php`

## New Controllers

- `app/Http/Controllers/Learning/FlashcardController.php`
- `app/Http/Controllers/Learning/QuizController.php`
- `app/Http/Controllers/PricingController.php`
- `app/Http/Controllers/StudyMatchingController.php`
- `app/Http/Controllers/StudyRoomController.php`
- `app/Http/Controllers/StudyRoomMessageController.php`

## New Middleware

- `app/Http/Middleware/EnsureUserCanCreateRoom.php`

## New Models

- `app/Models/Flashcard.php`
- `app/Models/FlashcardDeck.php`
- `app/Models/MatchQueueEntry.php`
- `app/Models/QuizQuestion.php`
- `app/Models/QuizSet.php`
- `app/Models/StudyMatch.php`
- `app/Models/StudyMatchMessage.php`
- `app/Models/StudyProfile.php`
- `app/Models/StudyRoom.php`
- `app/Models/StudyRoomMember.php`
- `app/Models/StudyRoomMessage.php`
- `app/Models/UserBlock.php`
- `app/Models/UserReport.php`

## New Services

- `app/Services/Learning/StudyMatchingService.php`
- `app/Services/Learning/StudyContentGenerator.php`
- `app/Services/Learning/MaterialTextExtractor.php`
- `app/Services/Learning/FlashcardReviewScheduler.php`

## New Migrations

- `database/migrations/2026_04_20_090000_add_product_fields_to_users_table.php`
- `database/migrations/2026_04_20_090100_create_study_profiles_table.php`
- `database/migrations/2026_04_20_090200_create_study_rooms_tables.php`
- `database/migrations/2026_04_20_090300_create_study_matching_tables.php`
- `database/migrations/2026_04_20_091000_create_flashcard_decks_table.php`
- `database/migrations/2026_04_20_091001_create_flashcards_table.php`
- `database/migrations/2026_04_20_091002_create_quiz_sets_table.php`
- `database/migrations/2026_04_20_091003_create_quiz_questions_table.php`

## New Seeders

- `database/seeders/TesterDemoSeeder.php`

## New / Added Views

- `resources/views/flashcards/index.blade.php`
- `resources/views/layouts/sidebar.blade.php`
- `resources/views/matchmaking/index.blade.php`
- `resources/views/matchmaking/show.blade.php`
- `resources/views/pricing.blade.php`
- `resources/views/quizzes/index.blade.php`
- `resources/views/rooms/index.blade.php`
- `resources/views/rooms/show.blade.php`

## New Tests

- `tests/Feature/StudyMatchingTest.php`
- `tests/Feature/StudyRoomTest.php`
- `tests/Unit/StudyContentGeneratorTest.php`

## Summary

Perubahan utama dalam sesi ini mencakup:

- pengamanan flow belajar berbasis user login
- penambahan fitur `flashcards` dan `quiz` yang aktif
- penambahan fitur `group chat kelas`
- penambahan fitur `study matching`
- penambahan halaman `pricing`
- penambahan CTA `Upgrade to Pro` untuk user `free`
- penambahan section `testimoni` dan update link `Harga` di landing page
- pemulihan link `Pomodoro` di sidebar
- penambahan dummy seeder untuk tester
- penambahan pipeline OCR gratis berbasis Tesseract/Poppler untuk material PDF scan dan gambar
- support Office modern ringan `.docx`, `.pptx`, `.xlsx` tanpa LibreOffice; format lama `.doc`, `.ppt`, `.xls` diarahkan untuk convert ulang
- hasil OCR dirapikan dengan AI OpenRouter jika `OPENAI_API_KEY` tersedia, lalu ringkasan otomatis dibuat di halaman `summaries`
- penambahan limit OCR akun free/premium melalui env `OCR_FREE_MAX_PAGES` dan `OCR_PREMIUM_MAX_PAGES`
- penyederhanaan OCR production: dependency cukup Tesseract + Poppler; LibreOffice dihapus dari Docker/env agar image lebih ringan
- upload material sekarang memprioritaskan isi file terlebih dahulu; textarea hanya menjadi fallback jika file gagal dibaca
- generator flashcard dan kuis sekarang mencoba AI OpenRouter terlebih dahulu dengan fallback generator lokal jika AI gagal/hasil JSON tidak valid
- output flashcard divalidasi agar front berupa istilah/konsep jelas dan back berupa definisi singkat
- output kuis divalidasi agar selalu 4 opsi unik dengan `correct_choice` index 0-3 dan pembahasan jawaban
- penambahan rate limit generate AI content melalui env `AI_CONTENT_FREE_PER_MINUTE`, `AI_CONTENT_FREE_PER_DAY`, dan `AI_CONTENT_PREMIUM_PER_MINUTE`

## Notes

- File ini mencerminkan kondisi perubahan saat ini, bukan histori langkah demi langkah.
- Pasca-merge, konflik utama sudah dibersihkan; modul admin `users` dan `documents` sudah diaktifkan kembali dengan backend yang disesuaikan ke arsitektur aktif proyek.
- Artefak pull yang tidak dipakai lagi seperti `Document` model, migration `documents`, dan seeder legacy sudah dibersihkan.
- `package.json` tidak diubah untuk OCR karena OCR berjalan di backend melalui binary server, bukan dependency frontend.
- LibreOffice tidak dipakai sebagai dependency production agar Docker/image tetap lebih ringan.
- Jika Anda ingin, saya bisa buat versi kedua yang berisi:
  - file + alasan perubahan per file
  - urutan perubahan kronologis
  - mana yang aman direvert dan mana yang jadi fondasi fitur
