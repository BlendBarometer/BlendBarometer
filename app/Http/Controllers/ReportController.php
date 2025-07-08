<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\GraphDescription;
use App\Models\Question_category;
use App\Models\Sub_category;
use App\Models\EmailRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image;

class ReportController extends Controller
{

    private int $pageNumber = 0;

    private $labelStyle = ['color' => '888888'];
    private $valueStyle = ['bold' => true];
    private $NotesTextBoxColor = '#BFBFBF';

    private $labelWidth = 1500;
    private $valueWidth = 3000;
    private $paddingWidth = 300;

    public function sendReport()
    {
        $phpWord = new PhpWord();
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 20, 'name' => 'Arial']);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 15, 'name' => 'Arial']);

        $fileName = 'BlendBarometer rapport ' . session('module') . ' ' . now()->format('d-m-Y') . '.docx';

        $this->addFrontPage($phpWord);
        $this->addTableOfContents($phpWord);
        $this->addInformationPage($phpWord);
        $this->addResults($phpWord);
        $this->addFillableNotes($phpWord);
        $this->addEndPage($phpWord);

        $writer = IOFactory::createWriter($phpWord);
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        $academy = session('academy');

        $specific = EmailRule::query()
            ->where('academy_name', $academy)
            ->pluck('email');

        if ($specific->isNotEmpty()) {
            $recipients = $specific->unique()->values();
        } else {
            $recipients = EmailRule::query()
                ->whereNull('academy_name')
                ->pluck('email')
                ->unique()
                ->values();
        }

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
            $mail->CharSet = 'UTF-8';
            $mail->isHTML(true);
            $mail->Subject = 'Resultaten BlendBarometer';
            $name = session('name');
            $academy = session('academy');
            $module = session('module');
            $date = now()->format('d-m-Y');
            $course = session('course');

            foreach ($recipients as $address) {
                $mail->addAddress($address);
            }

            $html = View::make('intermediate-report-email', [
                'name' => session('name'),
                'emailParticipant' => session('email'),
                'academy' => $academy,
                'module' => session('module'),
                'date' => now()->format('d-m-Y'),
                'summary' => session('summary'),
            ])->render();

            $mail->Body = $html;
            $mail->addAttachment($tempFile, $fileName);
            $mail->SMTPOptions = [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ];
            $mail->send();
        } catch (Exception $e) {
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

    private function addFrontPage($phpWord)
    {
        $titleFontSize = strlen(session('module')) > 30 ? 28 : 35;

        $this->pageNumber += 1;
        $section = $phpWord->addSection([
            'marginTop' => 500,
            'marginBottom' => 0,
            'marginLeft' => 600,
            'marginRight' => 600,
        ]);

        $section->addImage('https://blendbarometer.nl/images/report-background.png', [
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

        $imgtable->addCell(20000)->addImage('https://blendbarometer.nl/images/logo-avans-white.png', ['align' => Jc::START, 'width' => 100, 'height' => 30]);
        $imgtable->addCell(20000)->addImage('https://blendbarometer.nl/images/report-logo.png', ['align' => Jc::END, 'width' => 140, 'height' => 25]);

        $section->addTextBreak(1);

        $month = Carbon::now()->locale('nl')->isoFormat('MMMM YYYY');

        $section->addText('Tussenrapport - ' . session('module'), ['size' => $titleFontSize, 'bold' => true, 'color' => 'white'], ['alignment' => Jc::CENTER]);
        $section->addText('Blended Learning • ' . $month, ['size' => 15, 'color' => 'white'], ['alignment' => Jc::CENTER]);

        $section->addTextBreak(1);

        $section->addImage('https://blendbarometer.nl/images/introduction_image.png', [
            'alignment' => Jc::CENTER,
            'width' => 460,
            'height' => 460,
        ]);

        $infotable = $section->addTable([
            'alignment' => Jc::END,
        ]);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Academie', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText(session('academy-abbreviation'), $this->valueStyle);
        $infotable->addCell($this->paddingWidth);
        $infotable->addCell($this->labelWidth)->addText('Docent', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText(session('name'), $this->valueStyle);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Opleiding', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText(session('course'), $this->valueStyle);
        $infotable->addCell($this->paddingWidth);
        $infotable->addCell($this->labelWidth)->addText('ICTO Coach', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText('&lt;vul hier in&gt;', $this->valueStyle);

        $infotable->addRow();
        $infotable->addCell($this->labelWidth)->addText('Module', $this->labelStyle);
        $infotable->addCell($this->valueWidth)->addText(session('module'), $this->valueStyle);
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

        $section->addImage('https://blendbarometer.nl/images/report-background.png', [
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

        $imgtable->addCell(20000)->addImage('https://blendbarometer.nl/images/logo-avans-white.png', ['align' => Jc::START, 'width' => 100, 'height' => 30]);
        $imgtable->addCell(20000)->addImage('https://blendbarometer.nl/images/report-logo.png', ['align' => Jc::END, 'width' => 140, 'height' => 25]);

        $section->addTextBreak(3);

        $section->addImage('https://blendbarometer.nl/images/introduction_image.png', [
            'alignment' => Jc::CENTER,
            'width' => 460,
            'height' => 460,
        ]);
    }

    private function addInformationPage($phpWord)
    {
        $page = $this->createpage($phpWord);
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

        $table->addCell(3500)->addImage('https://blendbarometer.nl/images/barometer-report.png', [
            'alignment' => Jc::CENTER,
            'width' => 100,
            'height' => 100,
        ]);

        $page->addTitle('Over module', 1, $this->pageNumber);
        $date = now()->translatedFormat('j F Y');
        $moduleText = sprintf("Op %s heeft %s de barometer ingevuld voor de module %s van opleiding %s aan de %s.", Carbon::now()->locale('nl')->isoFormat('DD MMMM YYYY'), session('name'), session('module'), session('course'), session('academy'));
        $page->addText($moduleText, [
            'color' => '888888',
            'lineHeight' => 1.5,
        ]);

        $page->addTitle('Samenvatting module', 2, $this->pageNumber);

        $page->addText(session('summary'), [
            'color' => '888888',
            'lineHeight' => 1.5,
        ]);
    }

    private function addTableOfContents($phpWord)
    {
        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Inhoudsopgave', 1, $this->pageNumber);
        $page->addTOC();

        $page->addImage('https://blendbarometer.nl/images/barometer-report-2.png', [
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

        $page->addTextBox(['alignment' => Jc::CENTER, 'width' => 470, 'height' => 80, 'borderColor' => $this->NotesTextBoxColor])
            ->addText('Notities: ..................................................................................................................');
        $imageRelativePathRadar = 'images/temp/radar.png';
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

        $list1 = Sub_category::where('question_category_id', 1)->pluck('name');
        $list2 = Sub_category::where('question_category_id', 2)->pluck('name');

        $i = 0;
        $looping = true;
        while ($looping) {
            if (($i) % 2 == 0) {
                $page = $this->createPage($phpWord);
                $this->addStandardHeaderFooter($page);
                $table = $page->addTable([
                    'alignment' => Jc::CENTER,
                ]);
                $table->addRow();
                if ($i == 0) {
                    $table->addCell(6000)->addText('Fysieke leeractiviteiten', ['alignment' => Jc::START, 'bold' => true, 'size' => 15]);
                    $table->addCell(6000)->addText('Online leeractiviteiten', ['alignment' => Jc::END, 'bold' => true, 'size' => 15]);
                }
                $page->addTextBreak(2);
            }
            $name1 = null;
            $name2 = null;
            if ($i < $list1->count()) {
                $name1 = $list1[$i];
            }
            if ($i < $list2->count()) {
                $name2 = $list2[$i];
            }
            $this->newGraphRow($table, $name1, $name2);
            $i++;
            if ($i >= $list1->count() && $i >= $list2->count()) {
                $looping = false;
            }
        }

        $page = $this->createPage($phpWord);

        $imageRelativePathWheelInside = 'images/temp/wheelInside.png';
        $imagePathWheelInside = Storage::disk('public')->path($imageRelativePathWheelInside);

        $imageRelativePathWheelOutside = 'images/temp/wheelOutside.png';
        $imagePathWheelOutside = Storage::disk('public')->path($imageRelativePathWheelOutside);

        $imagePathWheelBarometerOutside = 'https://blendbarometer.nl/images/barometer-transparent.png';

        if (file_exists($imagePathWheelInside) && file_exists($imagePathWheelOutside) && file_exists($imagePathWheelBarometerOutside)) {

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

            $combinedPath = storage_path('app/public/images/temp/combined_with_white_background.png');
            imagepng($finalImage, $combinedPath);

            imagedestroy($foreground);
            imagedestroy($background);
            imagedestroy($border);
            imagedestroy($scaledBorder);
            imagedestroy($finalImage);

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
        } else {
            $page->addText('Grafiek niet gevonden.');
        }
    }

    private function sanitizeText($text)
    {
        if (!is_string($text)) return '';
        $text = preg_replace('/\&/', 'en', $text);
        return preg_replace('/[[:^print:]]/', '', $text);
    }

    private function newGraphRow($table, $name1, $name2)
    {
        $name1Here = $name1 != null;
        $name2Here = $name2 != null;

        $imageRelativePath1 = 'images/temp/physical' . $name1 . '.png';
        $imagePath1 = Storage::disk('public')->path($imageRelativePath1);

        $imageRelativePath2 = 'images/temp/online' . $name2 . '.png';
        $imagePath2 = Storage::disk('public')->path($imageRelativePath2);

        $table->addRow();
        $cell1 = $table->addCell(6000);
        $cell2 = $table->addCell(6000);

        if ($name1Here) {
            $cell1->addTextBreak(2);
            $this->addGraph($imagePath1, $cell1);
        }

        if ($name2Here) {
            $cell2->addTextBreak(2);
            $this->addGraph($imagePath2, $cell2);
        }

        $table->addRow();
        $cell1 = $table->addCell(6000);
        $cell2 = $table->addCell(6000);
        if ($name1Here) {
            $cell1->addText($name1, ['alignment' => Jc::START, 'bold' => true, 'size' => 13]);
        }
        if ($name2Here) {
            $cell2->addText($name2, ['alignment' => Jc::END, 'bold' => true, 'size' => 13]);
        }

        $table->addRow();
        $cell1 = $table->addCell(6000);
        $cell2 = $table->addCell(6000);
        if ($name1Here) {
            $cell1->addTextBox(['alignment' => Jc::START, 'width' => 230, 'height' => 70, 'borderColor' => $this->NotesTextBoxColor])
                ->addText('Notities: .........................................................');
        }
        if ($name2 != null) {
            $cell2->addTextBox(['alignment' => Jc::START, 'width' => 230, 'height' => 70, 'borderColor' => $this->NotesTextBoxColor])
                ->addText('Notities: .........................................................');
        }
    }

    private function addFillableNotes($phpWord)
    {
        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Verslag gesprek', 1, $this->pageNumber);
        $textrun = $page->addTextRun();
        $textrun->addText('Docent: ', $this->labelStyle);
        $textrun->addText('&lt;vul hier in&gt;', $this->valueStyle);

        $textrun = $page->addTextRun();
        $textrun->addText('Icto Coach: ', $this->labelStyle);
        $textrun->addText('&lt;vul hier in&gt;', $this->valueStyle);

        $textrun = $page->addTextRun();
        $textrun->addText('Datum gesprek: ', $this->labelStyle);
        $textrun->addText('&lt;vul hier in&gt;', $this->valueStyle);

        $page->addTitle('Verslag', 2, $this->pageNumber);
        $page->addText('&lt;vul hier in&gt;');

        $page = $this->createPage($phpWord);
        $this->addStandardHeaderFooter($page);

        $page->addTitle('Advies en Actiepunten', 1, $this->pageNumber);

        $page->addTitle('Advies', 2, $this->pageNumber);
        $page->addText('&lt;vul hier in&gt;');

        $page->addTitle('Actiepunten', 2, $this->pageNumber);
        $page->addText('&lt;vul hier in&gt;');
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

    function addStandardHeaderFooter($section)
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

        $table->addCell(5500)->addText('Blended Learning Rapport', array_merge($headerTextStyle, [
            'color' => '888888',
        ]), [
            'alignment' => Jc::START,
        ]);

        $table->addCell(5500)->addText(session('academy-abbreviation') . ' - ' . session('course') . ' - ' . session('module'), array_merge($headerTextStyle, [
            'color' => '888888',
            'bold' => true,
        ]), [
            'alignment' => Jc::END,
        ]);


        // --- FOOTER ---
        $footer = $section->addFooter();
        $footerTable = $footer->addTable(['alignment' => Jc::CENTER]);
        $footerTable->addRow();

        $footerTable->addCell(4000)->addImage('https://blendbarometer.nl/images/logo.png', [
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

    function unlinkImages()
    {
        $folderPath = storage_path('app/public/images/temp');

        if (File::exists($folderPath)) {
            $files = File::files($folderPath);

            foreach ($files as $file) {
                File::delete($file);
            }
        }
    }

    function addGraph($imagePath, $cell)
    {
        if (file_exists($imagePath)) {
            $cell->addImage($imagePath, [
                'width' => 245,
                'height' => 160,
                'alignment' => Jc::START,
            ]);
        } else {
            $cell->addText('Grafiek niet gevonden.');
        }
    }
}
