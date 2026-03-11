<?php

namespace Tests\Support;

use App\Data\SessionInfo;
use App\Http\Controllers\ReportController;
use Closure;
use PhpOffice\PhpWord\PhpWord;

class TestReportController extends ReportController
{
    private ?Closure $sectionComposer = null;

    public function withSessionInfo(SessionInfo $sessionInfo): void
    {
        $this->setSessionInfoForTesting($sessionInfo);
    }

    public function withImageBasePath(string $imageBasePath): void
    {
        $this->imageBasePath = $imageBasePath;
    }

    public function withSectionComposer(callable $composer): void
    {
        $this->sectionComposer = $composer instanceof Closure
            ? $composer
            : Closure::fromCallable($composer);
    }

    public function buildReport(): array
    {
        return $this->generateReportForTesting();
    }

    protected function composeReportSections(PhpWord $phpWord): void
    {
        if ($this->sectionComposer !== null) {
            ($this->sectionComposer)($phpWord);
            return;
        }

        parent::composeReportSections($phpWord);
    }
}
