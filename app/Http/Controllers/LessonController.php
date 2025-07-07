<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Sub_category;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function view($currentStep)
    {
        $totalSteps = Sub_category::count();
        $subCategory = Sub_category::orderBy('question_category_id')->orderBy('id')->get()[$currentStep - 1];
        $questions = $subCategory->questions;

        $answers = session()->get('lessonLevelData', []);

        $customQuestions = array_filter(
            $answers[$currentStep] ?? [],
            fn($key) => str_starts_with($key, 'custom_'),
            ARRAY_FILTER_USE_KEY
        );

        $intermediate = Content::where('section_name', 'intermediate_lesson')->firstOrFail();
        $previous = ($currentStep > 1 || $intermediate->show) ? route('lesson-level.previous', $currentStep) : route('information');

        return view(
            'lesson-level',
            compact('subCategory', 'questions', 'totalSteps', 'currentStep', 'answers', 'customQuestions', 'previous')
        );
    }

    public function next($currentStep)
    {
        $totalSteps = Sub_category::count();
        if ($currentStep < $totalSteps) {
                return redirect(route('lesson-level', $currentStep+1));
            }
        else{
                return redirect(route('intermediate.view', 'moduleniveau'));
            }
    }

    public function previous($currentStep)
    {
        $totalSteps = Sub_category::count();
        if ($currentStep <= 1) {
                return redirect(route('intermediate.view', 'lesniveau'));
            }
        else{
                return redirect(route('lesson-level', $currentStep - 1));
            }
    }

    public function submit(Request $request, $subCategoryId)
    {
        $answers = session()->get('lessonLevelData', []);

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questionId = str_replace('question_', '', $key);
                $answers[$subCategoryId][$questionId] = $value;
            }

            if (str_starts_with($key, 'custom_question_')) {
                $answers[$subCategoryId][$key] = $value;
            }
        }

        session()->put('lessonLevelData', $answers);
        $currentStep = $request->input('current_step');

        return redirect(route('lesson-level.next', $currentStep));
    }

    public function delete($id, $customQuestionId)
    {
        $answers = session()->get('lessonLevelData', []);
        if (isset($answers[$id]) && isset($answers[$id][$customQuestionId])) {
            unset($answers[$id][$customQuestionId]);
            session()->put('lessonLevelData', $answers);
        }
        return redirect()->back();
    }
}
