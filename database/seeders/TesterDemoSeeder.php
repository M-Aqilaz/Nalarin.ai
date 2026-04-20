<?php

namespace Database\Seeders;

use App\Models\AiSummary;
use App\Models\ChatThread;
use App\Models\FeatureUsage;
use App\Models\FlashcardDeck;
use App\Models\Material;
use App\Models\QuizSet;
use App\Models\StudyMatch;
use App\Models\StudyProfile;
use App\Models\StudyRoom;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TesterDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@nalarin.ai'],
            [
                'name' => 'Admin Nalarin',
                'password' => 'password',
                'role' => 'admin',
                'plan' => 'premium',
                'room_limit' => 999,
                'match_credits' => 999,
                'is_active' => true,
            ]
        );

        $freeUser = User::updateOrCreate(
            ['email' => 'free@tester.com'],
            [
                'name' => 'Free Tester',
                'password' => 'password',
                'role' => 'user',
                'plan' => 'free',
                'room_limit' => 2,
                'match_credits' => 3,
                'is_active' => true,
            ]
        );

        $premiumUser = User::updateOrCreate(
            ['email' => 'pro@tester.com'],
            [
                'name' => 'Pro Tester',
                'password' => 'password',
                'role' => 'user',
                'plan' => 'premium',
                'room_limit' => 10,
                'match_credits' => 99,
                'is_active' => true,
            ]
        );

        $this->seedProfile($freeUser, [
            'education_level' => 'SMA',
            'primary_subject' => 'Biologi',
            'goal' => 'Persiapan ujian semester',
            'study_style' => 'Diskusi santai',
            'bio' => 'Suka belajar bareng dan latihan soal singkat.',
            'availability' => 'Malam hari',
        ]);

        $this->seedProfile($premiumUser, [
            'education_level' => 'Mahasiswa',
            'primary_subject' => 'Sejarah',
            'goal' => 'Persiapan presentasi dan kuis mingguan',
            'study_style' => 'Active recall',
            'bio' => 'Fokus pada ringkasan cepat, flashcard, dan diskusi mendalam.',
            'availability' => 'Siang sampai malam',
        ]);

        $freeMaterial = $this->seedMaterialBundle(
            $freeUser,
            'Sistem Pencernaan Manusia',
            'sistem-pencernaan.pdf',
            'Sistem pencernaan adalah rangkaian organ yang mengolah makanan menjadi energi. Lambung merupakan organ yang memecah makanan dengan bantuan asam dan enzim. Usus halus adalah tempat penyerapan nutrisi utama. Enzim membantu mempercepat reaksi kimia dalam proses pencernaan. Nutrisi adalah zat penting yang dibutuhkan tubuh untuk tumbuh dan berfungsi dengan baik.'
        );

        $premiumMaterial = $this->seedMaterialBundle(
            $premiumUser,
            'Sejarah Kemerdekaan Indonesia',
            'sejarah-kemerdekaan.pdf',
            'Vacuum of power terjadi setelah Jepang menyerah kepada Sekutu pada Agustus 1945 dan sebelum Sekutu tiba sepenuhnya di Indonesia. Soekarno dan Hatta dibawa ke Rengasdengklok oleh golongan muda. Proklamasi dibacakan pada 17 Agustus 1945 di Jalan Pegangsaan Timur 56. Peristiwa ini menandai lahirnya negara Indonesia merdeka.'
        );

        $this->seedRoom($premiumUser, [$premiumUser, $freeUser], 'Kelas Biologi Intensif', 'Biologi', 'Room belajar untuk review konsep, latihan cepat, dan tanya jawab.');
        $this->seedRoom($freeUser, [$freeUser], 'Kelas Persiapan Ujian', 'Ujian Sekolah', 'Room sederhana untuk siswa free plan.');

        $this->seedMatch($freeUser, $premiumUser);

        $this->seedFeatureUsage();

        // Keep admin easy to inspect with one owned material too.
        Material::updateOrCreate(
            ['user_id' => $admin->id, 'title' => 'Admin Monitoring Notes'],
            [
                'original_filename' => null,
                'file_path' => null,
                'mime_type' => 'text/plain',
                'file_size' => 0,
                'raw_text' => 'Catatan admin untuk memonitor usage, report, dan onboarding tester.',
                'status' => 'processed',
            ]
        );
    }

    private function seedProfile(User $user, array $attributes): void
    {
        StudyProfile::updateOrCreate(
            ['user_id' => $user->id],
            [...$attributes, 'is_matchmaking_enabled' => true]
        );
    }

    private function seedMaterialBundle(User $user, string $title, string $filename, string $rawText): Material
    {
        $material = Material::updateOrCreate(
            ['user_id' => $user->id, 'title' => $title],
            [
                'original_filename' => $filename,
                'file_path' => 'materials/' . $filename,
                'mime_type' => 'application/pdf',
                'file_size' => 248320,
                'raw_text' => $rawText,
                'status' => 'processed',
            ]
        );

        AiSummary::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => $user->id, 'title' => 'Ringkasan ' . $title],
            [
                'summary_text' => Str::limit($rawText, 220),
                'model' => 'local-placeholder',
            ]
        );

        $thread = ChatThread::updateOrCreate(
            ['user_id' => $user->id, 'material_id' => $material->id, 'title' => 'Diskusi ' . $title],
            []
        );

        $thread->messages()->delete();
        $thread->messages()->createMany([
            ['role' => 'user', 'content' => 'Tolong jelaskan inti materi ini secara singkat.'],
            ['role' => 'assistant', 'content' => 'Intinya adalah memahami konsep utama, contoh, dan istilah penting dari materi ' . $title . '.'],
        ]);

        $this->seedDeck($material, $title);
        $this->seedQuiz($material, $title);

        return $material;
    }

    private function seedDeck(Material $material, string $title): void
    {
        $deck = FlashcardDeck::updateOrCreate(
            ['material_id' => $material->id],
            [
                'title' => 'Smart Flashcards: ' . $title,
                'description' => 'Deck dummy untuk tester.',
                'card_count' => 4,
            ]
        );

        $deck->cards()->delete();
        $deck->cards()->createMany([
            ['front' => 'Konsep 1', 'back' => 'Definisi singkat konsep utama dari materi ini.', 'example' => 'Contoh penerapan konsep 1.', 'difficulty' => 'Dasar', 'sort_order' => 1],
            ['front' => 'Konsep 2', 'back' => 'Penjelasan lanjutan yang masih mudah diingat.', 'example' => 'Contoh penerapan konsep 2.', 'difficulty' => 'Menengah', 'sort_order' => 2],
            ['front' => 'Konsep 3', 'back' => 'Hubungan antar istilah penting di dalam materi.', 'example' => 'Contoh penerapan konsep 3.', 'difficulty' => 'Menengah', 'sort_order' => 3],
            ['front' => 'Konsep 4', 'back' => 'Ringkasan poin yang sering keluar dalam kuis.', 'example' => 'Contoh penerapan konsep 4.', 'difficulty' => 'Dasar', 'sort_order' => 4],
        ]);
    }

    private function seedQuiz(Material $material, string $title): void
    {
        $quiz = QuizSet::updateOrCreate(
            ['material_id' => $material->id],
            [
                'title' => 'Latihan Kuis: ' . $title,
                'description' => 'Kuis dummy untuk tester.',
                'question_count' => 4,
            ]
        );

        $quiz->questions()->delete();
        $quiz->questions()->createMany([
            [
                'prompt' => 'Apa fokus utama materi ini?',
                'choices' => ['Konsep utama', 'Warna favorit', 'Harga barang', 'Cuaca'],
                'correct_choice' => 0,
                'explanation' => 'Materi dummy ini selalu menekankan konsep utama.',
                'sort_order' => 1,
            ],
            [
                'prompt' => 'Pilihan mana yang paling relevan untuk latihan belajar?',
                'choices' => ['Bermain game', 'Mengingat istilah penting', 'Tidur siang', 'Menonton iklan'],
                'correct_choice' => 1,
                'explanation' => 'Istilah penting dipakai sebagai basis latihan.',
                'sort_order' => 2,
            ],
            [
                'prompt' => 'Bagaimana cara terbaik memahami materi dummy ini?',
                'choices' => ['Diskusi dan latihan soal', 'Mengabaikannya', 'Menebak semua jawaban', 'Hanya membaca judul'],
                'correct_choice' => 0,
                'explanation' => 'Flow aplikasi ini memang mendorong diskusi dan latihan.',
                'sort_order' => 3,
            ],
            [
                'prompt' => 'Fitur apa yang paling cocok setelah membaca materi?',
                'choices' => ['Flashcards dan quiz', 'Belanja online', 'Edit video', 'Kalkulator pajak'],
                'correct_choice' => 0,
                'explanation' => 'Deck dan kuis dipakai untuk retensi belajar.',
                'sort_order' => 4,
            ],
        ]);
    }

    private function seedRoom(User $owner, array $members, string $name, string $topic, string $description): void
    {
        $room = StudyRoom::updateOrCreate(
            ['slug' => Str::slug($name)],
            [
                'owner_id' => $owner->id,
                'name' => $name,
                'topic' => $topic,
                'description' => $description,
                'visibility' => 'public',
                'max_members' => 30,
                'is_active' => true,
            ]
        );

        $room->members()->delete();
        foreach ($members as $index => $member) {
            $room->members()->create([
                'user_id' => $member->id,
                'role' => $index === 0 ? 'owner' : 'member',
                'status' => 'active',
                'joined_at' => now()->subDays(2 - min($index, 1)),
            ]);
        }

        $room->messages()->delete();
        $room->messages()->createMany([
            ['user_id' => $owner->id, 'content' => 'Selamat datang di room ' . $name . '.'],
            ['user_id' => $members[count($members) - 1]->id, 'content' => 'Siap belajar bareng dan diskusi materi hari ini.'],
        ]);
    }

    private function seedMatch(User $userOne, User $userTwo): void
    {
        $match = StudyMatch::updateOrCreate(
            ['user_one_id' => $userOne->id, 'user_two_id' => $userTwo->id, 'topic' => 'Diskusi Materi Campuran'],
            [
                'status' => 'active',
                'matched_at' => now()->subHour(),
            ]
        );

        $match->messages()->delete();
        $match->messages()->createMany([
            ['user_id' => $userOne->id, 'content' => 'Hai, mau review materi bareng malam ini?'],
            ['user_id' => $userTwo->id, 'content' => 'Boleh, kita mulai dari ringkasan lalu lanjut ke kuis.'],
        ]);
    }

    private function seedFeatureUsage(): void
    {
        foreach ([
            'Unggah Materi' => 18,
            'Ringkasan Otomatis' => 14,
            'AI Tutor Khusus' => 12,
            'Smart Flashcards' => 11,
            'Latihan Kuis' => 10,
            'Group Chat Kelas' => 8,
            'Study Matching' => 6,
        ] as $featureName => $clickCount) {
            FeatureUsage::updateOrCreate(
                ['feature_name' => $featureName],
                ['click_count' => $clickCount]
            );
        }
    }
}
