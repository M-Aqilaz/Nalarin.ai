<?php

namespace Tests\Unit;

use App\Models\Material;
use App\Services\Learning\StudyContentGenerator;
use PHPUnit\Framework\TestCase;

class StudyContentGeneratorTest extends TestCase
{
    public function test_it_generates_flashcards_from_material_text(): void
    {
        $material = new Material([
            'title' => 'Sistem Pencernaan',
            'raw_text' => 'Sistem pencernaan adalah rangkaian organ yang mengolah makanan menjadi energi. '
                . 'Lambung merupakan organ yang memecah makanan dengan bantuan asam dan enzim. '
                . 'Usus halus adalah tempat penyerapan nutrisi utama. '
                . 'Enzim membantu mempercepat reaksi kimia dalam proses pencernaan. '
                . 'Nutrisi adalah zat penting yang dibutuhkan tubuh untuk tumbuh dan berfungsi dengan baik.',
        ]);

        $generator = new StudyContentGenerator();
        $cards = $generator->generateFlashcards($material);

        $this->assertNotEmpty($cards);
        $this->assertArrayHasKey('front', $cards[0]);
        $this->assertArrayHasKey('back', $cards[0]);
        $this->assertArrayHasKey('difficulty', $cards[0]);
    }

    public function test_it_generates_multiple_choice_questions_from_material_text(): void
    {
        $material = new Material([
            'title' => 'Fotosintesis',
            'raw_text' => 'Fotosintesis adalah proses tumbuhan mengubah cahaya menjadi energi kimia. '
                . 'Klorofil merupakan pigmen hijau yang membantu menyerap cahaya matahari. '
                . 'Glukosa adalah hasil utama fotosintesis yang dipakai tumbuhan sebagai sumber energi. '
                . 'Stomata adalah pori-pori daun yang membantu pertukaran gas. '
                . 'Karbon dioksida adalah gas yang dibutuhkan tumbuhan saat fotosintesis.',
        ]);

        $generator = new StudyContentGenerator();
        $questions = $generator->generateQuiz($material);

        $this->assertGreaterThanOrEqual(4, count($questions));
        $this->assertArrayHasKey('prompt', $questions[0]);
        $this->assertArrayHasKey('choices', $questions[0]);
        $this->assertArrayHasKey('correct_choice', $questions[0]);
        $this->assertIsArray($questions[0]['choices']);
        $this->assertGreaterThanOrEqual(2, count($questions[0]['choices']));
    }
}
