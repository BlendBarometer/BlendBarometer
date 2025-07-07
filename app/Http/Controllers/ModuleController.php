<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Module_level_answer;
use App\Models\Question_category;
use App\Models\Sub_category;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function getModuleLevel($currentStep)
    {
        $category = Question_category::where('form_section_id', HomeController::MODULE_INDEX)->with('questions')->get()[$currentStep - 1];

        $totalSteps = Question_category::where('form_section_id', HomeController::MODULE_INDEX)->count();

        $descriptions = Module_level_answer::pluck('description', 'answer');

        $answers = session()->get('moduleLevelData', []);

        $moduleAnswers = Module_level_answer::all();

        $intermediate = Content::where('section_name', 'intermediate_module')->firstOrFail();
        $previous = ($currentStep > 1 || $intermediate->show) ? route('module-level.previous', $currentStep) : route('lesson-level', Sub_category::count());
        return view('module-level', compact('category', 'answers', 'currentStep', 'totalSteps', 'descriptions', 'previous', 'moduleAnswers'));
    }

    public function submit(Request $request, $currentStep)
    {
        $answers = session()->get('moduleLevelData', []);

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'question_')) {
                $questionId = str_replace('question_', '', $key);
                $answers[$currentStep][$questionId] = $value;
            }
        }

        session()->put('moduleLevelData', $answers);

        return redirect(route('module-level.next', $currentStep));
    }

    public function next($currentStep)
    {
        $totalSteps = Question_category::where('form_section_id', 2)->count();

        if ($currentStep >= $totalSteps) {
            return redirect(route('intermediate.view', 'resultaten'));
        } else {
            return redirect(route('module-level', $currentStep + 1));
        }
    }

    public function previous(Request $request, $currentStep)
    {
        $this->submit($request, $currentStep);

        if ($currentStep <= 1) {
            return redirect(route('intermediate.view', 'moduleniveau'));
        } else {
            return redirect(route('module-level', $currentStep - 1));
        }
    }
}
