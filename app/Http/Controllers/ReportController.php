<?php

namespace App\Http\Controllers;

use App\Data\SessionInfo;
use App\Models\Content;
use App\Models\GraphDescription;
use App\Models\ModuleInformationAnswer;
use App\Models\ModuleInformationField;
use App\Models\Question_category;
use App\Models\Sub_category;
use App\Models\EmailRule;
use App\Support\Whitespace;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\Settings;

class ReportController extends Controller
{
    private int $pageNumber = 0;
    private SessionInfo $sessionInfo;
    protected string $imageBasePath = 'images/';

    private function imagePath(string $imageName): string
    {
        $path = public_path("{$this->imageBasePath}{$imageName}");

        if (!File::exists($path)) {
            throw new \RuntimeException("Image not found: {$path}");
        }

        return $path;
    }

    private $labelStyle = ['color' => '888888'];
    private $valueStyle = ['bold' => true];
    private $NotesTextBoxColor = '#BFBFBF';

    private $labelWidth = 1500;
    private $valueWidth = 3000;
    private $paddingWidth = 300;

    public function sendReport()
    {
        $this->sessionInfo = $this->extractSessionInfo();
        $moduleInformationValues = $this->resolveModuleInformationValuesByKey([
            'summary' => $this->sessionInfo->summary,
            'goals' => $this->sessionInfo->goals,
            'evaluation' => $this->sessionInfo->evaluation,
        ]);

        ['tempFile' => $tempFile, 'fileName' => $fileName] = $this->generateReport();

        $academy = $this->sessionInfo->academy;

        $specific = EmailRule::query()
            ->where('academy_name', $academy)
            ->pluck('email');

        $recipients = ($specific->isNotEmpty()) ? $specific->unique()->values() : EmailRule::query()
            ->whereNull('academy_name')
            ->pluck('email')
            ->unique()
            ->values();

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'email' => 'Er is geen e-mailregel gevonden om het rapport naartoe te sturen.',
            ]);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT');
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = 'Resultaten BlendBarometer';
            $name = $this->sessionInfo->name;
            $academy = $this->sessionInfo->academy;
            $module = $this->sessionInfo->module;
            $date = now()->format('d-m-Y');
            $course = $this->sessionInfo->course;

            foreach ($recipients as $address) {
                $mail->addAddress($address);
            }

            $html = View::make('intermediate-report-email', [
                'name' => $this->sessionInfo->name,
                'emailParticipant' => $this->sessionInfo->email,
                'academy' => $academy,
                'module' => $this->sessionInfo->module,
                'date' => now()->format('d-m-Y'),
                'summary' => $moduleInformationValues['summary'],
                'goals' => $moduleInformationValues['goals'],
                'evaluation' => $moduleInformationValues['evaluation'],
            ])->render();

            $mail->Body = $html;
            $mail->addAttachment($tempFile, $fileName);
            $mail->send();
        } catch (Exception $e) {
            $this->unlinkImages();
            session()->flush();
            Log::error('Report Mail send failed: ' . $e->getMessage());

            return redirect()
                ->route('confirmation')
                ->withErrors('error', 'Fout bij het verzenden van de e-mail: ' . $e->getMessage());
        }

        session()->flush();
        $this->unlinkImages();

        return redirect()->route('confirmation')->with('success', [
            'ictoCoach' => $recipients->implode(', '),
            'academy' => $academy,
            'course' => $course,
            'module' => $module,
            'teacher' => $name,
            'date' => $date,
        ]);
    }

    private function generateReport(): array
    {
        $phpWord = new PhpWord();
        // document is ongeldig als er niet-escaped tekens in staan, dus we moeten escaping forceren
        Settings::setOutputEscapingEnabled(true);
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20, 'name' => 'Arial']);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 15, 'name' => 'Arial']);

        $safeModuleName = $this->sanitizeFileNamePart($this->sessionInfo->module);
        $currentDate = now()->format('d-m-Y');
        $fileName = "BlendBarometer rapport $safeModuleName $currentDate.docx";

        $this->composeReportSections($phpWord);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $prefix = "blendreport_";
        $tempFile = tempnam(sys_get_temp_dir(), $prefix);
        if ($tempFile === false) {
            throw new \RuntimeException('Kon geen tijdelijk bestand aanmaken voor het rapport.');
        }

        $writer->save($tempFile);
        $this->assertReportIntegrity($tempFile);

        return [
            'tempFile' => $tempFile,
            'fileName' => $fileName,
        ];
    }

    protected function composeReportSections(PhpWord $phpWord): void
    {
        $this->addFrontPage($phpWord);
        $this->addTableOfContents($phpWord);
        $this->addInformationPage($phpWord);
        $this->addResults($phpWord);
        $this->addFillableNotes($phpWord);
        $this->addEndPage($phpWord);
    }

    protected function setSessionInfoForTesting(SessionInfo $sessionInfo): void
    {
        $this->sessionInfo = $sessionInfo;
    }

    protected function generateReportForTesting(): array
    {
        return $this->generateReport();
    }

    private function extractSessionInfo(): SessionInfo
    {
        $moduleInformationValues = $this->resolveModuleInformationValuesByKey([
            'summary' => (string) session('summary', ''),
            'goals' => (string) session('goals', ''),
            'evaluation' => (string) session('evaluation', ''),
        ]);

        return new SessionInfo(
            name: $this->sanitizeReportText((string) session('name', '')),
            email: $this->sanitizeReportText((string) session('email', '')),
            academy: $this->sanitizeReportText((string) session('academy', '')),
            academyAbbreviation: $this->sanitizeReportText((string) session('academy-abbreviation', '')),
            module: $this->sanitizeReportText((string) session('module', '')),
            course: $this->sanitizeReportText((string) session('course', '')),
            summary: $this->sanitizeReportText($moduleInformationValues['summary']),
            goals: $this->sanitizeReportText($moduleInformationValues['goals']),
            evaluation: $this->sanitizeReportText($moduleInformationValues['evaluation']),
            sessionUid: $this->sanitizeReportText((string) session('session_uid', '')),
        );
    }

    private function addFrontPage($phpWord)
    {
        $titleFontSize = strlen($this->sessionInfo->module) > 30 ? 28 : 35;

        $this->pageNumber += 1;
        $section = $phpWord->addSection([
            'marginTop' => 500,
            'marginBottom' => 0,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        $section->addImage($this->imagePath('report-background.png'), [
            'width' => 1000,
            'height' => 600,
            'positioning' => 'absolute',
            'posHorizontalRel' => 'page',
            'posHorizontal' => Image::POSITION_HORIZONTAL_LEFT,
            'posVerticalRel' => 'page',
            'posVertical' => Image::POSITION_VERTICAL_TOP,
            'wrappingStyle' => 'behind',
        ]);


        $imgtable = $section->addTable();
        $imgtable->addRow();

        $imgtable->addCell(20000)->addImage($this->imagePath('logo-avans-white.png'), ['align' => Jc::START, 'width' => 100, 'height' => 30]);
        $imgtable->addCell(20000)->addImage($this->imagePath('report-logo.png'), ['align' => Jc::END, 'width' => 140, 'height' => 25]);

        $section->addTextBreak(1);

        $month = Carbon::now()->locale('nl')->isoFormat('MMMM YYYY');

        $section->addText("Tussenrapport - {$this->sessionInfo->module}", ['size' => $titleFontSize, 'bold' => true, 'color' => 'white'], ['alignment' => Jc::CENTER]);
        $section->addText("Blended Learning • $month", ['size' => 15, 'color' => 'white'], ['alignment' => Jc::CENTER]);

        $section->addTextBreak(1);

        $section->addImage($this->imagePath('introduction_image.png'), [
            'alignment' => Jc::CENTER,
            'width' => 460,
            'height' => 460,
        ]);

        $infotable = $section->addTable([
            'alignment' => Jc::END,
        ]);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Academie', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText($this->sessionInfo->academyAbbreviation, $this->valueStyle);
        $infotable->addCell($this->paddingWidth);
        $infotable->addCell($this->labelWidth)->addText('Docent', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText($this->sessionInfo->name, $this->valueStyle);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Opleiding', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText($this->sessionInfo->course, $this->valueStyle);
        $infotable->addCell($this->paddingWidth);
        $infotable->addCell($this->labelWidth)->addText('ICTO Coach', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText('vul hier in', $this->valueStyle);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Module', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText($this->sessionInfo->module, $this->valueStyle);
        $infotable->addCell($this->paddingWidth);
        $infotable->addCell($this->labelWidth)->addText('Datum', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText(now()->format('d-m-Y'), $this->valueStyle);
    }

    private function addEndPage($phpWord)
    {
        $section = $phpWord->addSection([
            'marginTop' => 500,
            'marginBottom' => 0,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        $section->addImage($this->imagePath('report-background.png'), [
            'width' => 1000,
            'height' => 600,
            'positioning' => 'absolute',
            'posHorizontalRel' => 'page',
            'posHorizontal' => Image::POSITION_HORIZONTAL_LEFT,
            'posVerticalRel' => 'page',
            'posVertical' => Image::POSITION_VERTICAL_TOP,
            'wrappingStyle' => 'behind',
        ]);


        $imgtable = $section->addTable();
        $imgtable->addRow();

        $imgtable->addCell(20000)->addImage($this->imagePath('logo-avans-white.png'), ['align' => Jc::START, 'width' => 100, 'height' => 30]);
        $imgtable->addCell(20000)->addImage($this->imagePath('report-logo.png'), ['align' => Jc::END, 'width' => 140, 'height' => 25]);

        $section->addTextBreak(3);

        $section->addImage($this->imagePath('introduction_image.png'), [
            'alignment' => Jc::CENTER,
            'width' => 460,
            'height' => 460,
        ]);
    }

    private function addInformationPage($phpWord)
    {
        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTextBreak(1);

        $page->addTitle('De BlendBarometer', 1, $this->pageNumber);

        $table = $page->addTable([
            'alignment' => Jc::CENTER,
        ]);
        $table->addRow();

        $plainText = strip_tags(html_entity_decode(Content::where('section_name', 'intro_description')->first()->info));
        $text1 = preg_replace('/\s+/', ' ', $plainText);

        $table->addCell(6500)->addText($text1, [
            'color' => '888888',
            'lineHeight' => 1.5,
        ]);

        $table->addCell(3500)->addImage($this->imagePath('barometer-report.png'), [
            'alignment' => Jc::CENTER,
            'width' => 100,
            'height' => 100,
        ]);

        $page->addTitle('Over module', 1, $this->pageNumber);
        $date = Carbon::now()->locale('nl')->isoFormat('DD MMMM YYYY');
        $moduleText = "Op {$date} heeft {$this->sessionInfo->name} de barometer ingevuld voor de module {$this->sessionInfo->module} van opleiding {$this->sessionInfo->course} aan de {$this->sessionInfo->academy}.";
        $page->addText($moduleText, [
            'color' => '888888',
            'lineHeight' => 1.5,
        ]);

        $this->addModuleSubjectSections($page);
    }

    private function addModuleSubjectSections($page): void
    {
        foreach ($this->getModuleSubjectEntries() as $entry) {
            $this->addModuleSubjectSection($page, $entry['title'], $entry['content']);
        }
    }

    private function addModuleSubjectSection($page, string $title, string $content): void
    {
        if (trim($content) === '') {
            return;
        }

        $page->addTitle($title, 2, $this->pageNumber);
        $page->addText($content, [
            'color' => '888888',
            'lineHeight' => 1.5,
        ]);
    }

    private function getModuleSubjectEntries(): array
    {
        $fields = $this->getActiveModuleInformationFields();
        if ($fields === null) {
            return [
                ['title' => 'Samenvatting module', 'content' => $this->sessionInfo->summary],
                ['title' => 'Leeruitkomsten module', 'content' => $this->sessionInfo->goals],
                ['title' => 'Toetsing module', 'content' => $this->sessionInfo->evaluation],
            ];
        }

        $answersByField = [];

        if (Auth::check()) {
            $answersByField = ModuleInformationAnswer::query()
                ->where('user_id', Auth::id())
                ->whereIn('module_information_field_id', $fields->pluck('id'))
                ->pluck('answer', 'module_information_field_id')
                ->toArray();
        }

        return $fields->map(function (ModuleInformationField $field) use ($answersByField): array {
            $fallback = $this->getFallbackModuleSubjectValue($field->key);
            $content = (string) ($answersByField[$field->id] ?? session($field->key, $fallback));

            return [
                'title' => $this->sanitizeReportText($field->title),
                'content' => $this->sanitizeReportText($content),
            ];
        })->all();
    }

    private function getFallbackModuleSubjectValue(string $key): string
    {
        return match ($key) {
            'summary' => $this->sessionInfo->summary,
            'goals' => $this->sessionInfo->goals,
            'evaluation' => $this->sessionInfo->evaluation,
            default => '',
        };
    }

    private function getActiveModuleInformationFields(): ?\Illuminate\Support\Collection
    {
        if (!Schema::hasTable('module_information_field') || !Schema::hasTable('module_information_answer')) {
            return null;
        }

        $fields = ModuleInformationField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $fields->isEmpty() ? null : $fields;
    }

    private function resolveModuleInformationValuesByKey(array $fallbackValues): array
    {
        $fields = $this->getActiveModuleInformationFields();
        if ($fields === null || !Auth::check()) {
            return $fallbackValues;
        }

        $matchingFields = $fields->whereIn('key', array_keys($fallbackValues));
        if ($matchingFields->isEmpty()) {
            return $fallbackValues;
        }

        $answersByField = ModuleInformationAnswer::query()
            ->where('user_id', Auth::id())
            ->whereIn('module_information_field_id', $matchingFields->pluck('id'))
            ->pluck('answer', 'module_information_field_id')
            ->toArray();

        foreach ($matchingFields as $field) {
            $fallbackValues[$field->key] = (string) ($answersByField[$field->id] ?? $fallbackValues[$field->key]);
        }

        return $fallbackValues;
    }

    private function addTableOfContents($phpWord)
    {
        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Inhoudsopgave', 1, $this->pageNumber);
        $page->addTOC();

        $page->addImage($this->imagePath('barometer-report-2.png'), [
            'width' => 220,
            'height' => 220,
            'alignment' => Jc::CENTER,
        ]);
    }

    private function addResults($phpWord)
    {
        $lessonLevelGeneralDescription = GraphDescription::select('description')->where('graph_type', 'lesson-level-general')->pluck('description');
        $moduleLevelGeneralDescription = GraphDescription::select('description')->where('graph_type', 'module-level-general')->pluck('description');

        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Resultaten', 1, $this->pageNumber);

        $tempId = $this->sessionInfo->sessionUid;
        $imageRelativePathRadar = "images/temp/{$tempId}_radar.png";
        $imagePathRadar = Storage::disk('public')->path($imageRelativePathRadar);

        if (file_exists($imagePathRadar)) {
            $page->addImage($imagePathRadar, [
                'width' => 500,
                'height' => 500,
                'alignment' => Jc::CENTER,
            ]);
            $page->addText('Lesniveau - Algemeen', ['alignment' => Jc::START, 'bold' => true, 'size' => 13]);
            $page->addText($lessonLevelGeneralDescription[0]);
        } else {
            $page->addText('Grafiek niet gevonden.');
        }

        $page->addTextBox(['alignment' => Jc::CENTER, 'width' => 470, 'height' => 80])
            ->addText('hier komt notities te staan');

        $subCategories = Sub_category::orderBy('id')->get();

        foreach ($subCategories as $subCategory) {
            $page = $this->createPage($phpWord);
            $this->addStandardHeaderFooter($page);

            // Title
            $page->addTitle(trim(Whitespace::replaceAll((string) $subCategory->name, '-'), '-'), 2, $this->pageNumber);

            // Graph table
            $graphTable = $page->addTable(['alignment' => Jc::CENTER]);
            $graphTable->addRow();

            $tempId = $this->sessionInfo->sessionUid;
            $cleanedName = trim(Whitespace::replaceAll((string) $subCategory->name, '-'), '-');
            $physicalImagePath = Storage::disk('public')->path("images/temp/{$tempId}_physical{$cleanedName}.png");
            $onlineImagePath = Storage::disk('public')->path("images/temp/{$tempId}_online{$cleanedName}.png");

            $cell1 = $graphTable->addCell(6000);
            $cell2 = $graphTable->addCell(6000);

            if (file_exists($physicalImagePath)) {
                $this->addGraph($physicalImagePath, $cell1);
            }

            if (file_exists($onlineImagePath)) {
                $this->addGraph($onlineImagePath, $cell2);
            }

            // Labels row
            $graphTable->addRow();
            $graphTable->addCell(6000)->addText('Fysiek', ['alignment' => Jc::CENTER, 'size' => 11]);
            $graphTable->addCell(6000)->addText('Online', ['alignment' => Jc::CENTER, 'size' => 11]);

            // Explanation
            $page->addTextBreak(1);
            $page->addText('Uitleg:', ['bold' => true, 'size' => 12]);
            $description = GraphDescription::where('sub_category_id', $subCategory->id)->first();
            if ($description) {
                $page->addText($description->description);
            } else {
                $page->addText('Geen beschrijving beschikbaar.');
            }

            // Notes box
            $page->addTextBreak(1);
            $page->addTextBox(['alignment' => Jc::CENTER, 'width' => 470, 'height' => 90])
                ->addText('hier komt notities te staan');
        }

        $page = $this->createPage($phpWord);

        $tempId = $this->sessionInfo->sessionUid;
        $imageRelativePathWheelInside = "images/temp/{$tempId}_wheelInside.png";
        $imagePathWheelInside = Storage::disk('public')->path($imageRelativePathWheelInside);

        $imageRelativePathWheelOutside = "images/temp/{$tempId}_wheelOutside.png";
        $imagePathWheelOutside = Storage::disk('public')->path($imageRelativePathWheelOutside);

        $imagePathWheelBarometerOutside = $this->imagePath('barometer-transparent.png');

        if (!file_exists($imagePathWheelInside) || !file_exists($imagePathWheelOutside)) {
            $page->addText('Grafiek niet gevonden.');
            return;
        }

        $foreground = imagecreatefrompng($imagePathWheelInside);
        $background = imagecreatefrompng($imagePathWheelOutside);
        $border = imagecreatefrompng($imagePathWheelBarometerOutside);

        $bgWidth = imagesx($background);
        $bgHeight = imagesy($background);

        $finalImage = imagecreatetruecolor($bgWidth, $bgHeight);
        $white = imagecolorallocate($finalImage, 255, 255, 255);
        imagefill($finalImage, 0, 0, $white);

        imagealphablending($finalImage, true);

        imagecopy($finalImage, $background, 0, 0, 0, 0, $bgWidth, $bgHeight);

        $foregroundWidth = imagesx($foreground);
        $foregroundHeight = imagesy($foreground);
        $foregroundX = ($bgWidth - $foregroundWidth) / 2;
        $foregroundY = ($bgHeight - $foregroundHeight) / 2;
        imagecopy($finalImage, $foreground, $foregroundX, $foregroundY, 0, 0, $foregroundWidth, $foregroundHeight);

        $borderWidth = imagesx($border);
        $borderHeight = imagesy($border);
        $scaledBorder = imagecreatetruecolor($bgWidth, $bgHeight);
        imagealphablending($scaledBorder, false);
        imagesavealpha($scaledBorder, true);
        imagecopyresampled($scaledBorder, $border, 0, 0, 0, 0, $bgWidth, $bgHeight, $borderWidth, $borderHeight);

        imagecopy($finalImage, $scaledBorder, 0, 0, 0, 0, $bgWidth, $bgHeight);

        $combinedPath = Storage::disk('public')->path("images/temp/{$tempId}_combined_with_white_background.png");
        imagepng($finalImage, $combinedPath);

        $page->addImage($combinedPath, [
            'width' => 350,
            'height' => 350,
            'alignment' => Jc::CENTER,
        ]);

        $legendItems = \App\Models\Graph_legenda::all();
        if ($legendItems->count() > 0) {
            $colorLegend = $page->addTable();
            foreach ($legendItems as $item) {
                $colorLegend->addRow();
                $colorLegend->addCell(2000)->addText($item->name, ['bgColor' => ltrim($item->color, '#')]);
                $colorLegend->addCell(4000)->addText($item->description, $this->valueStyle);
            }
        }

        $page->addText('Moduleniveau', ['alignment' => Jc::START, 'bold' => true, 'size' => 13]);
        $page->addText($moduleLevelGeneralDescription[0]);


        $items = Question_category::join('question', 'question_category.id', '=', 'question.question_category_id')
            ->select('question.text')
            ->whereIn('question.question_category_id', [3, 4, 5])
            ->pluck('question.text')
            ->all();

        $page->addText('Legenda', ['alignment' => Jc::START, 'bold' => true, 'size' => 13]);
        $legend = $page->addTable();

        for ($j = 0; $j < count($items); $j += 2) {
            $legend->addRow();

            $legend->addCell(300)->addText((string)$j + 1, $this->labelStyle);
            $legend->addCell(4000)->addText($this->sanitizeText($items[$j]), $this->valueStyle);

            $legend->addCell($this->paddingWidth)->addText('', []);

            if (isset($items[$j + 1])) {
                $legend->addCell(300)->addText((string)$j + 2, $this->labelStyle);
                $legend->addCell(4000)->addText($this->sanitizeText($items[$j + 1]), $this->valueStyle);
            } else {
                $legend->addCell(200)->addText('', $this->labelStyle);
                $legend->addCell(5000)->addText('', $this->valueStyle);
            }
        }
    }

    private function sanitizeText($text)
    {
        if (!is_string($text)) return '';
        $text = preg_replace('/\&/', 'en', $text);
        return preg_replace('/[[:^print:]]/', '', $text);
    }


    private function addFillableNotes($phpWord)
    {
        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Verslag gesprek', 1, $this->pageNumber);
        $textrun = $page->addTextRun();
        $textrun->addText('Docent: ', $this->labelStyle);
        $textrun->addText('vul hier in', $this->valueStyle);

        $textrun = $page->addTextRun();
        $textrun->addText('Icto Coach: ', $this->labelStyle);
        $textrun->addText('vul hier in', $this->valueStyle);

        $textrun = $page->addTextRun();
        $textrun->addText('Datum gesprek: ', $this->labelStyle);
        $textrun->addText('vul hier in', $this->valueStyle);

        $page->addTitle('Verslag', 2, $this->pageNumber);
        $page->addText('vul hier in');

        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Advies en Actiepunten', 1, $this->pageNumber);

        $page->addTitle('Advies', 2, $this->pageNumber);
        $page->addText('vul hier in');

        $page->addTitle('Actiepunten', 2, $this->pageNumber);
        $page->addText('vul hier in');
    }

    private function createPage($phpWord)
    {
        $this->pageNumber += 1;
        return $phpWord->addSection([
            'marginTop' => 400,
            'marginBottom' => 0,
            'marginLeft' => 900,
            'marginRight' => 900,
        ]);
    }

    private function addStandardHeaderFooter($section)
    {
        // --- Header ---
        $header = $section->addHeader();
        $table = $header->addTable([
            'alignment' => Jc::CENTER,
        ]);
        $table->addRow();

        $headerTextStyle = [
            'bold' => true,
            'size' => 10,
        ];

        $table->addCell(5500)->addText('Blended Learning Rapport', [...$headerTextStyle, 'color' => '888888'], [
            'alignment' => Jc::START,
        ]);

        $table->addCell(5500)->addText(
            "{$this->sessionInfo->academyAbbreviation} - {$this->sessionInfo->course} - {$this->sessionInfo->module}",
            [
                ...$headerTextStyle,
                'color' => '888888',
                'bold' => true
            ],
            ['alignment' => Jc::END,]
        );

        // --- FOOTER ---
        $footer = $section->addFooter();
        $footerTable = $footer->addTable(['alignment' => Jc::CENTER]);
        $footerTable->addRow();

        $footerTable->addCell(4000)->addImage($this->imagePath('logo.png'), [
            'width' => 90,
            'height' => 16,
            'alignment' => Jc::START,
        ]);

        $date = now()->format('d-m-Y');
        $footerTable->addCell(3000)->addText($date, [
            'size' => 10,
        ], [
            'alignment' => Jc::CENTER,
        ]);

        $footerTable->addCell(4000)->addPreserveText('Pagina {PAGE} van {NUMPAGES}', [
            'size' => 10,
        ], [
            'alignment' => Jc::END,
        ]);
    }

    private function unlinkImages()
    {
        $folderPath = storage_path('app/public/images/temp');
        if (!File::exists($folderPath)) {
            return;
        }

        $files = File::files($folderPath);
        foreach ($files as $file) {
            File::delete($file);
        }
    }

    private function addGraph($imagePath, $cell)
    {
        if (!file_exists($imagePath)) {
            $cell->addText('Grafiek niet gevonden.');
            return;
        }
        $cell->addImage($imagePath, [
            'width' => 245,
            'height' => 160,
            'alignment' => Jc::START,
        ]);
    }

    private function sanitizeReportText(string $text): string
    {
        // Keep only characters that are valid in XML 1.0 to prevent broken DOCX XML.
        return preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $text) ?? '';
    }

    private function sanitizeFileNamePart(string $value): string
    {
        $value = $this->sanitizeReportText($value);
        $value = preg_replace('/[\\\\\/\:\*\?\"\<\>\|]/', '-', $value) ?? '';
        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $value !== '' ? $value : 'Module';
    }

    private function assertReportIntegrity(string $docxPath): void
    {
        if (!is_readable($docxPath) || filesize($docxPath) === 0) {
            throw new \RuntimeException("Rapportbestand is leeg of niet leesbaar.");
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            throw new \RuntimeException("Rapportbestand is geen geldig DOCX archief.");
        }

        $requiredEntries = [
            '[Content_Types].xml',
            '_rels/.rels',
            'word/document.xml',
        ];

        foreach ($requiredEntries as $entry) {
            if ($zip->locateName($entry) === false) {
                $zip->close();
                throw new \RuntimeException("DOCX mist vereist onderdeel: $entry");
            }
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($documentXml === false || trim($documentXml) === '') {
            throw new \RuntimeException("DOCX bevat geen geldige document.xml inhoud.");
        }

        $previous = libxml_use_internal_errors(true);
        $parsed = simplexml_load_string($documentXml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($parsed === false) {
            throw new \RuntimeException("DOCX document.xml is ongeldig XML.");
        }
    }
}
