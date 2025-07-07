<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\GraphDescription;
use App\Models\Question;
use App\Models\Question_category;
use App\Models\Sub_category;
use App\Models\Graph_legenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResultsController extends Controller
{
    public function view()
    {
        if ($this->hasInsufficientData()) {
            return redirect(route('home'));
        }

        $lessonLevelPhysicalSubcategories = Sub_category::select('id', 'name')->where('question_category_id', 1)->get();
        $lessonLevelOnlineSubcategories = Sub_category::select('id', 'name')->where('question_category_id', 2)->get();

        $lessonLevelPhysicalQuestions = Question::select('sub_category_id', 'text')->where('question_category_id', 1)->get();
        $lessonLevelPhysicalQuestions = $lessonLevelPhysicalQuestions->mapToGroups(function ($item, $key) {
            return [$item['sub_category_id'] => $item->text];
        });

        $lessonLevelOnlineQuestions = Question::select('sub_category_id', 'text')->where('question_category_id', 2)->get();
        $lessonLevelOnlineQuestions = $lessonLevelOnlineQuestions->mapToGroups(function ($item, $key) {
            return [$item['sub_category_id'] => $item->text];
        });

        $lessonLevelGeneralDescription = GraphDescription::select('description')->where('graph_type', 'lesson-level-general')->get();
        $lessonLevelPhysicalDescriptions = GraphDescription::select('sub_category_id', 'description')->where('graph_type', 'physical')->get();
        $lessonLevelOnlineDescriptions = GraphDescription::select('sub_category_id', 'description')->where('graph_type', 'online')->get();
        $moduleLevelGeneralDescription = GraphDescription::select('description')->where('graph_type', 'module-level-general')->get();

        $subCategoryPhysicalIds = Sub_category::select('id')->where('question_category_id', 1)->pluck('id')->toArray();

        $answers = session()->get("lessonLevelData");
        foreach ($answers as $subCat => $answerPage) {
            foreach ($answerPage as $key => $answer) {
                if (str_starts_with($key, "custom_question_")) {
                    $parts = explode('_', $key);
                    $questionName = $parts[2];
                    if (in_array($subCat, $subCategoryPhysicalIds)) {
                        $lessonLevelPhysicalQuestions = $lessonLevelPhysicalQuestions->put(
                            $subCat,
                            $lessonLevelPhysicalQuestions->get($subCat, collect())->push($questionName)
                        );
                    } else {
                        $lessonLevelOnlineQuestions = $lessonLevelOnlineQuestions->put(
                            $subCat,
                            $lessonLevelOnlineQuestions->get($subCat, collect())->push($questionName)
                        );
                    }
                }
            }
        }

        $lessonLevelDataPhysical = [];
        foreach ($lessonLevelPhysicalSubcategories as $subCat) {
            $subCatId = $subCat->id;
            $total = 0;
            if (isset($answers[$subCatId])) {
                foreach ($answers[$subCatId] as $key => $answer) {
                    $total += (is_numeric($answer) ? $answer : 0);
                }
            }
            $lessonLevelDataPhysical[] = $total;
        }
        $lessonLevelOnlineSubcats = Sub_category::select('id', 'name')->where('question_category_id', 2)->get();
        $lessonLevelDataOnline = [];
        foreach ($lessonLevelOnlineSubcats as $subCat) {
            $subCatId = $subCat->id;
            $total = 0;
            if (isset($answers[$subCatId])) {
                foreach ($answers[$subCatId] as $key => $answer) {
                    $total += (is_numeric($answer) ? $answer : 0);
                }
            }
            $lessonLevelDataOnline[] = $total;
        }

        $moduleLevelCategories = Question_category::join('question', 'question_category.id', '=', 'question.question_category_id')
            ->select('question_category.name', 'question.text', 'question.label')
            ->where('form_section_id', 2)
            ->get();

        $moduleLevelCategories = $moduleLevelCategories->mapToGroups(function ($item, $key) {
            return [$item['name'] => $item->text];
        });

        // Get question labels for module level
        $moduleLevelLabels = Question::select('label')
            ->where('question_category_id', '>', 2)
            ->orderBy('question_category_id')
            ->orderBy('id')
            ->pluck('label')
            ->toArray();

        $legends = Graph_legenda::get();
        $intermediate = Content::where('section_name', 'intermediate_results')->firstOrFail();
        $previous = $intermediate->show
            ? route('intermediate.view', 'resultaten')
            : route('module-level', Question_category::where('form_section_id', HomeController::MODULE_INDEX)->count());

        return view('results', [
            'lessonLevelPhysicalSubcategories' => $lessonLevelPhysicalSubcategories,
            'lessonLevelOnlineSubcategories' => $lessonLevelOnlineSubcategories,
            'lessonLevelPhysicalQuestions' => $lessonLevelPhysicalQuestions,
            'lessonLevelOnlineQuestions' => $lessonLevelOnlineQuestions,
            'lessonLevelGeneralDescription' => $lessonLevelGeneralDescription,
            'moduleLevelGeneralDescription' => $moduleLevelGeneralDescription,
            'lessonLevelPhysicalDescriptions' => $lessonLevelPhysicalDescriptions,
            'lessonLevelOnlineDescriptions' => $lessonLevelOnlineDescriptions,
            'lessonLevelDataOnline' => $lessonLevelDataOnline,
            'lessonLevelDataPhysical' => $lessonLevelDataPhysical,
            'lessonLevelDataAll' => $answers,
            'moduleLevelCategories' => $moduleLevelCategories,
            'moduleLevelLabels' => $moduleLevelLabels,
            'legends' => $legends,
            'previous' => $previous
        ]);
    }

    public function overviewAndResultsInfoView()
    {
        if ($this->hasInsufficientData()) {
            return redirect(route('home'));
        }

        $categoryCount = Question_category::where('form_section_id', 2)->count();
        return view('overview-and-results-info', compact('categoryCount'));
    }

    public function overviewAndSendView()
    {
        if ($this->hasInsufficientData()) {
            return redirect(route('home'));
        }

        return view('overview-and-send');
    }

    private function hasInsufficientData()
    {
        $lessonLevelData = session()->get('lessonLevelData');
        $moduleLevelData = session()->get('moduleLevelData');

        return !$lessonLevelData || !$moduleLevelData;
    }

    public function saveChart(Request $request)
    {
        // Retrieve the base64 image data from the incoming request
        $base64 = $request->input('image');
        $name = $request->input('name');

        if ($base64) {
            $base64 = str_replace('data:image/png;base64,', '', $base64);
            $base64 = str_replace(' ', '+', $base64);  // Replace spaces with '+' as per the base64 standard

            $imageData = base64_decode($base64);

            // Check if base64 decoding succeeded
            if ($imageData === false) {
                return response()->json(['error' => 'Base64 decoding failed'], 400);
            }

            // Define the path where the image will be saved
            $imagePath = 'images/temp/' . $name . '.png';

            $saved = Storage::disk('public')->put($imagePath, $imageData);

            if ($saved) {
                return response()->json(['message' => 'Image saved successfully', 'path' => $imagePath]);
            } else {
                return response()->json(['error' => 'Failed to save image'], 500);
            }
        }
        return response()->json(['error' => 'No image data received'], 400);
    }
}
