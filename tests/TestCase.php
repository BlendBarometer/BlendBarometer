<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function saveReportArtifactIfEnabled(string $sourcePath, string $targetFileName, bool $enabledByDefault = false): void
    {
        if (!env('SAVE_REPORT_TEST_ARTIFACT', $enabledByDefault)) {
            return;
        }

        $targetPath = storage_path('app/testing/' . ltrim($targetFileName, '/\\'));
        @mkdir(dirname($targetPath), 0777, true);
        @unlink($targetPath);

        if (@copy($sourcePath, $targetPath)) {
            fwrite(STDOUT, PHP_EOL . 'Saved report to: ' . $targetPath . PHP_EOL);
        } else {
            fwrite(STDOUT, PHP_EOL . 'Could not save report artifact to: ' . $targetPath . PHP_EOL);
        }
    }
}
