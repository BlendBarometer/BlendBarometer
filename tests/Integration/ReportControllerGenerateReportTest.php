<?php

namespace Tests\Integration;

use App\Data\SessionInfo;
use App\Models\Content;
use App\Models\Graph_legenda;
use App\Models\GraphDescription;
use App\Models\Module_level_answer;
use App\Models\Question;
use App\Models\Question_category;
use App\Models\Sub_category;
use App\Support\Whitespace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Support\TestReportController;

class ReportControllerGenerateReportTest extends TestCase
{
    use RefreshDatabase;

    private const SESSION_UID = 'test-session-uid';

    private string $fixtureImageDir;

    private function createSessionInfo(): SessionInfo
    {
        return new SessionInfo(
            name: 'Test Teacher',
            email: 'test.teacher@example.com',
            academy: 'Test Academy',
            academyAbbreviation: 'TA',
            module: 'Test Module',
            course: 'Test Course',
            summary: 'This is a test summary for the module.',
            sessionUid: self::SESSION_UID,
        );
    }

    /**
     * Create local PNGs for the static/remote images (backgrounds, logos, etc.)
     * so PhpWord doesn't need to fetch from blendbarometer.nl.
     */
    private function createStaticFixtureImages(): void
    {
        $this->fixtureImageDir = sys_get_temp_dir() . '/report-test-images-' . uniqid() . '/';
        mkdir($this->fixtureImageDir, 0777, true);

        $imageNames = [
            'report-background.png',
            'logo-avans-white.png',
            'report-logo.png',
            'introduction_image.png',
            'barometer-report.png',
            'barometer-report-2.png',
            'barometer-transparent.png',
            'logo.png',
        ];

        foreach ($imageNames as $name) {
            $img = imagecreatetruecolor(10, 10);
            imagesavealpha($img, true);
            imagepng($img, $this->fixtureImageDir . $name);
        }
    }

    private function cleanupStaticFixtureImages(): void
    {
        if (isset($this->fixtureImageDir) && is_dir($this->fixtureImageDir)) {
            array_map('unlink', glob($this->fixtureImageDir . '*'));
            rmdir($this->fixtureImageDir);
        }
    }

    private function seedDatabase(): void
    {
        // Content
        Content::create([
            'section_name' => 'intro_description',
            'info' => '<p>De BlendBarometer is een meetinstrument om de kwaliteit van een Blended module te meten.</p>',
        ]);

        // Form sections (needed as FK for question_category)
        DB::table('form_section')->insert([
            ['id' => 1, 'content_id' => 1, 'description' => 'Les niveau'],
            ['id' => 2, 'content_id' => 1, 'description' => 'Module niveau'],
        ]);

        // Question categories
        Question_category::create(['id' => 1, 'form_section_id' => 1, 'name' => 'Fysieke leeractiviteiten']);
        Question_category::create(['id' => 2, 'form_section_id' => 1, 'name' => 'Online leeractiviteiten']);
        Question_category::create(['id' => 3, 'form_section_id' => 2, 'name' => 'Samenhangend']);
        Question_category::create(['id' => 4, 'form_section_id' => 2, 'name' => 'Organisatorisch']);
        Question_category::create(['id' => 5, 'form_section_id' => 2, 'name' => 'Didactisch']);

        // Sub categories (physical = cat 1, online = cat 2)
        $physicalSubs = ['Samenwerken', 'Onderzoeken'];
        foreach ($physicalSubs as $i => $name) {
            Sub_category::create(['id' => $i + 1, 'question_category_id' => 1, 'name' => $name]);
        }
        $onlineSubs = ['Samenwerken', 'Onderzoeken'];
        foreach ($onlineSubs as $i => $name) {
            Sub_category::create(['id' => $i + 3, 'question_category_id' => 2, 'name' => $name]);
        }

        // Questions for categories 1 & 2 (lesson level)
        Question::create(['question_category_id' => 1, 'sub_category_id' => 1, 'text' => 'Post-it sessie', 'label' => 'Post-its']);
        Question::create(['question_category_id' => 2, 'sub_category_id' => 3, 'text' => 'MS Teams', 'label' => 'Teams']);

        // Questions for categories 3, 4, 5 (module level — used in legend)
        Question::create(['question_category_id' => 3, 'sub_category_id' => null, 'text' => 'Ondersteunt de Blend de Constructive alignment?', 'label' => 'Alignment']);
        Question::create(['question_category_id' => 3, 'sub_category_id' => null, 'text' => 'Past de technologie bij de leeruitkomsten?', 'label' => 'Tech match']);
        Question::create(['question_category_id' => 4, 'sub_category_id' => null, 'text' => 'Synchrone en asynchrone momenten?', 'label' => 'Sync/Async']);
        Question::create(['question_category_id' => 4, 'sub_category_id' => null, 'text' => 'Mix van online en fysiek?', 'label' => 'Online/Fysiek']);
        Question::create(['question_category_id' => 5, 'sub_category_id' => null, 'text' => 'Harmonieuze mix van leeractiviteiten?', 'label' => 'Activiteiten mix']);
        Question::create(['question_category_id' => 5, 'sub_category_id' => null, 'text' => 'Weten docenten en studenten hoe ze technologie gebruiken?', 'label' => 'Tech kennis']);

        // Graph descriptions
        GraphDescription::create(['graph_type' => 'lesson-level-general', 'description' => 'Overzicht lesniveau scores.']);
        GraphDescription::create(['graph_type' => 'module-level-general', 'description' => 'Overzicht moduleniveau scores.']);

        // Module level answers (FK for graph_legenda)
        foreach (['N.v.t.', 'Verkennen', 'Toepassen', 'Duidelijk plan', 'Verankerd'] as $answer) {
            Module_level_answer::create(['answer' => $answer]);
        }

        // Graph legenda
        Graph_legenda::create(['color' => '#FC2200', 'name' => 'Rood', 'description' => 'Lage score', 'module_level_answer_id' => 2]);
        Graph_legenda::create(['color' => '#38A772', 'name' => 'Groen', 'description' => 'Sterke score', 'module_level_answer_id' => 5]);
    }

    /**
     * Create minimal 1x1 PNGs for all chart images the report expects.
     */
    private function createFixtureImages(): void
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory('images/temp');

        $uid = self::SESSION_UID;

        $imageNames = [
            "{$uid}_radar.png",
            "{$uid}_wheelInside.png",
            "{$uid}_wheelOutside.png",
            // Physical subcategories (spaces replaced with hyphens)
            "{$uid}_physicalSamenwerken.png",
            "{$uid}_physicalOnderzoeken.png",
            // Online subcategories
            "{$uid}_onlineSamenwerken.png",
            "{$uid}_onlineOnderzoeken.png",
        ];

        foreach ($imageNames as $name) {
            $path = $disk->path("images/temp/{$name}");
            $img = imagecreatetruecolor(10, 10);
            imagepng($img, $path);
        }
    }

    private function cleanupFixtureImages(): void
    {
        Storage::disk('public')->deleteDirectory('images/temp');
    }

    private function invokeGenerateReport(?SessionInfo $sessionInfo = null): array
    {
        $controller = new TestReportController();
        $controller->withImageBasePath($this->fixtureImageDir);
        $controller->withSessionInfo($sessionInfo ?? $this->createSessionInfo());

        return $controller->buildReport();
    }

    private function assertDocxIntegrity(string $tempFile): void
    {
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempFile) === true, 'DOCX should be a valid zip archive.');
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('_rels/.rels'));
        $this->assertNotFalse($zip->locateName('word/document.xml'));

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertNotFalse($xml);
        $this->assertNotSame('', trim($xml));
        $this->assertNotFalse(simplexml_load_string($xml));
    }

    private function readDocumentXml(string $tempFile): string
    {
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($tempFile) === true, 'DOCX should be a valid zip archive.');
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        $this->assertNotFalse($xml);

        return $xml;
    }

    public function test_generate_full_report_produces_valid_docx(): void
    {
        $this->seedDatabase();
        $this->createStaticFixtureImages();
        $this->createFixtureImages();

        try {
            $result = $this->invokeGenerateReport();

            // Basic structure
            $this->assertArrayHasKey('tempFile', $result);
            $this->assertArrayHasKey('fileName', $result);
            $this->assertFileExists($result['tempFile']);
            $this->assertStringStartsWith('BlendBarometer rapport Test Module ', $result['fileName']);
            $this->assertStringEndsWith('.docx', $result['fileName']);

            $this->assertDocxIntegrity($result['tempFile']);

            $xml = $this->readDocumentXml($result['tempFile']);

            // Front page
            $this->assertStringContainsString('Tussenrapport', $xml);
            $this->assertStringContainsString('Test Module', $xml);
            $this->assertStringContainsString('TA', $xml); // academy abbreviation
            $this->assertStringContainsString('Test Teacher', $xml);
            $this->assertStringContainsString('Test Course', $xml);

            // Table of contents page
            $this->assertStringContainsString('Inhoudsopgave', $xml);

            // Information page
            $this->assertStringContainsString('De BlendBarometer', $xml);
            $this->assertStringContainsString('Over module', $xml);
            $this->assertStringContainsString('test summary', $xml);

            // Results page
            $this->assertStringContainsString('Resultaten', $xml);
            $this->assertStringContainsString('Lesniveau - Algemeen', $xml);
            $this->assertStringContainsString('Moduleniveau', $xml);
            $this->assertStringContainsString('Legenda', $xml);

            // Fillable notes pages
            $this->assertStringContainsString('Verslag gesprek', $xml);
            $this->assertStringContainsString('Advies en Actiepunten', $xml);
            $this->assertStringContainsString('Actiepunten', $xml);

            // Verify no missing-graph fallbacks appeared
            $this->assertStringNotContainsString('Grafiek niet gevonden', $xml);

            if (env('SAVE_REPORT_TEST_ARTIFACT', false)) {
                $target = storage_path('app/testing/last-integration-report.docx');
                @mkdir(dirname($target), 0777, true);
                @unlink($target);
                if (@copy($result['tempFile'], $target)) {
                    fwrite(STDOUT, PHP_EOL . 'Saved report to: ' . $target . PHP_EOL);
                } else {
                    fwrite(STDOUT, PHP_EOL . 'Could not save report artifact to: ' . $target . PHP_EOL);
                }
            }

            @unlink($result['tempFile']);
        } finally {
            $this->cleanupFixtureImages();
            $this->cleanupStaticFixtureImages();
        }
    }

    public function test_generate_report_handles_missing_chart_images_gracefully(): void
    {
        $this->seedDatabase();
        $this->createStaticFixtureImages();
        // Deliberately do NOT create chart fixture images

        try {
            $result = $this->invokeGenerateReport();

            $this->assertFileExists($result['tempFile']);
            $this->assertDocxIntegrity($result['tempFile']);

            $xml = $this->readDocumentXml($result['tempFile']);

            // Should still contain page structure
            $this->assertStringContainsString('Inhoudsopgave', $xml);
            $this->assertStringContainsString('Resultaten', $xml);

            // Should show fallback text for missing charts
            $this->assertStringContainsString('Grafiek niet gevonden', $xml);

            @unlink($result['tempFile']);
        } finally {
            $this->cleanupStaticFixtureImages();
        }
    }

    public function test_generate_report_with_complex_input_data_produces_valid_docx(): void
    {
        $this->seedDatabase();
        $this->createStaticFixtureImages();
        $this->createFixtureImages();

        $complexSessionInfo = new SessionInfo(
            name: "Docent 😀\n\r" . chr(1) . chr(2),
            email: 'complex.teacher+qa@example.com',
            academy: 'Academie <Test> & Partners',
            academyAbbreviation: 'A&T',
            module: 'Module: QA/Stress *Test*',
            course: "Course met unicode Ω en emoji 🚀",
            summary: "Samenvatting met lastige tekens: <tag * & \"quotes\' \` en control" . chr(7) . "\nNieuwe regel",
            sessionUid: self::SESSION_UID,
        );

        try {
            $result = $this->invokeGenerateReport($complexSessionInfo);

            $this->assertFileExists($result['tempFile']);
            $this->assertDocxIntegrity($result['tempFile']);
            $this->assertStringNotContainsString(':', $result['fileName']);
            $this->assertStringNotContainsString('/', $result['fileName']);
            $this->assertStringNotContainsString('*', $result['fileName']);

            $xml = $this->readDocumentXml($result['tempFile']);
            $this->assertStringContainsString('Academie', $xml);
            $this->assertStringContainsString('Resultaten', $xml);

            if (env('SAVE_REPORT_TEST_ARTIFACT', false)) {
                $target = storage_path('app/testing/last-complex-report.docx');
                @mkdir(dirname($target), 0777, true);
                if (@copy($result['tempFile'], $target)) {
                    fwrite(STDOUT, PHP_EOL . 'Saved report to: ' . $target . PHP_EOL);
                } else {
                    fwrite(STDOUT, PHP_EOL . 'Could not save report artifact to: ' . $target . PHP_EOL);
                }
            }
            @unlink($target);
        } finally {
            $this->cleanupFixtureImages();
            $this->cleanupStaticFixtureImages();
        }
    }

    public function test_generate_report_matches_chart_files_for_multiple_unicode_whitespace_variants(): void
    {
        $this->seedDatabase();
        $this->createStaticFixtureImages();
        $this->createFixtureImages();

        $spaceVariants = [
            " ",        // Regular space
            "\u{00A0}", // No-break space
            "\u{202F}", // Narrow no-break space
            "\u{2007}", // Figure space
            "\u{2009}", // Thin space
            "\u{3000}", // Ideographic space
            "\t",       // Horizontal tab
        ];

        $disk = Storage::disk('public');
        $nextId = 999;

        foreach ($spaceVariants as $spaceChar) {
            $subCategoryName = "Informatie{$spaceChar}verwerven";

            Sub_category::create([
                'id' => $nextId,
                'question_category_id' => 1,
                'name' => $subCategoryName,
            ]);

            Question::create([
                'question_category_id' => 1,
                'sub_category_id' => $nextId,
                'text' => 'Zoekstrategieën toepassen',
                'label' => 'Zoekstrategieën',
            ]);

            $normalized = Whitespace::replaceAll($subCategoryName, '-');
            $normalized = trim($normalized, '-');

            $extraImage = $disk->path('images/temp/' . self::SESSION_UID . '_physical' . $normalized . '.png');
            $img = imagecreatetruecolor(10, 10);
            imagepng($img, $extraImage);

            $nextId++;
        }

        try {
            $result = $this->invokeGenerateReport();

            $this->assertFileExists($result['tempFile']);
            $this->assertDocxIntegrity($result['tempFile']);

            $xml = $this->readDocumentXml($result['tempFile']);

            foreach ($spaceVariants as $spaceChar) {
                $subCategoryName = "Informatie{$spaceChar}verwerven";
                $normalized = Whitespace::replaceAll($subCategoryName, '-');
                $normalized = trim($normalized, '-');
                $this->assertStringContainsString($normalized, $xml);
            }

            $this->assertStringNotContainsString('Grafiek niet gevonden', $xml);

            @unlink($result['tempFile']);
        } finally {
            $this->cleanupFixtureImages();
            $this->cleanupStaticFixtureImages();
        }
    }
}
