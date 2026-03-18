<?php

namespace Tests\Feature;

use App\Data\SessionInfo;
use PhpOffice\PhpWord\PhpWord;
use Tests\TestCase;
use Tests\Support\TestReportController;

class ReportControllerGenerateReportTest extends TestCase
{
    public function test_generate_report_creates_docx_file_with_expected_filename_and_content(): void
    {
        $controller = new TestReportController();
        $controller->withSectionComposer(function (PhpWord $phpWord): void {
            $section = $phpWord->addSection();
            $section->addText('Generated report test content');
        });

        $controller->withSessionInfo(new SessionInfo(
            name: 'Test Teacher',
            email: 'teacher@example.com',
            academy: 'Test Academy',
            academyAbbreviation: 'TA',
            module: 'Test Module',
            course: 'Test Course',
            summary: 'Summary',
            sessionUid: 'session-123'
        ));

        /** @var array{tempFile: string, fileName: string} $result */
        $result = $controller->buildReport();

        $this->assertArrayHasKey('tempFile', $result);
        $this->assertArrayHasKey('fileName', $result);
        $this->assertFileExists($result['tempFile']);

        $this->assertStringStartsWith('BlendBarometer rapport Test Module ', $result['fileName']);
        $this->assertStringEndsWith('.docx', $result['fileName']);

        $zip = new \ZipArchive();
        $opened = $zip->open($result['tempFile']);

        $this->assertTrue($opened === true, 'Generated DOCX should be a readable zip archive.');

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        $this->assertNotFalse($documentXml);
        $this->assertStringContainsString('Generated report test content', $documentXml);

        if (env('SAVE_REPORT_TEST_ARTIFACT', false)) {
            $target = storage_path('app/testing/last-generate-report.docx');
            @mkdir(dirname($target), 0777, true);
            if (@copy($result['tempFile'], $target)) {
                fwrite(STDOUT, PHP_EOL . 'Saved report to: ' . $target . PHP_EOL);
            } else {
                fwrite(STDOUT, PHP_EOL . 'Could not save report artifact to: ' . $target . PHP_EOL);
            }
        }
        @unlink($result['tempFile']);
    }
}
