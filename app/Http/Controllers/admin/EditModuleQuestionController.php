<?php

namespace App\Http\Controllers\admin;

use App\Models\Content;
use App\Models\Sub_category;
use App\Models\Question_category;
use App\Models\Form_section;
use App\Models\Module_level_answer;
use App\Models\Question;
use App\Models\GraphDescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditModuleQuestionController
{
    public function index(): View
    {
        $categories = Question_category::where('form_section_id', 2)->get();
        $questions = collect();
        $formSections = Form_section::all();
        $moduleLevelAnswer = Module_level_answer::all();

        foreach ($categories as $cat) {
            $categorieQuestions = Question::where("question_category_id", $cat->id)->get();
            $questions = $questions->merge($categorieQuestions);
        }
        return view(
            'admin.edit-module-questions',
            [
                'categories' => $categories,
                'questions' => $questions,
                'formSections' => $formSections,
                'moduleLevelAnswer' => $moduleLevelAnswer

            ]
        );
    }

    public function updateQuestion(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required'],
            'label' => ['required'],
            'description' => ['nullable'],
        ]);

        Question::where('id', $request->question_id)->update([
            'text' => $request->input('text'),
            'label' => $request->input('label'),
            'description' => $request->input('description'),
        ]);
        return redirect()->route('admin.edit-module-questions');
    }

    public function createQuestion(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required'],
            'label' => ['required'],
            'description' => ['nullable'],
        ]);

        Question::insert([
            'question_category_id' => $request->input('question_category_id'),
            'text' => $request->input('text'),
            'description' => $request->input('description'),
            'label' => $request->input('label')
        ]);
        return redirect()->route('admin.edit-module-questions');
    }

    public function deleteQuestion($id): RedirectResponse
    {
        Question::where('id', $id)->delete();
        return redirect()->route('admin.edit-module-questions');
    }

    public function updateCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required'],
            'form_section_id' => ['required'],
        ]);

        $toUpdate = Question_category::find($request->input('category_id'));

        if ((string)$request->input('form_section_id') == (string)$toUpdate->form_section_id) {
            $toUpdate->update([
                'name' => $request->input('name'),
            ]);
        } else {
            Question::where('question_category_id', $toUpdate->id)->delete();
            $toUpdate->delete();

            $sub1 = Sub_category::create([
                'question_category_id' => 1,
                'name' => $request->input('name'),
            ]);
            $sub2 = Sub_category::create([
                'question_category_id' => 2,
                'name' => $request->input('name'),
            ]);

            GraphDescription::create([
                'graph_type' => 'physical', 
                'sub_category_id' => $sub1->id,
                'description' => 'Lorem ipsum dolor sit amet, consecte adipiscing elit, sed do eiusmod teincididunt ut labore et dolore magna aliqua.',
            ]);
            GraphDescription::create([
                'graph_type' => 'online',
                'sub_category_id' => $sub2->id,
                'description' => 'Lorem ipsum dolor sit amet, consecte adipiscing elit, sed do eiusmod teincididunt ut labore et dolore magna aliqua.',
            ]);
        }

        return redirect()->route('admin.edit-module-questions');
    }

    public function createCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required'],
            'form_section_id' => ['required'],
        ]);


        if ($request->input('form_section_id') == 1) {
            $sub1 = Sub_category::create([
                'question_category_id' => 1,
                'name' => $request->input('name'),
            ]);
            $sub2 = Sub_category::create([
                'question_category_id' => 2,
                'name' => $request->input('name'),
            ]);

            GraphDescription::create([
                'graph_type' => 'physical', 
                'sub_category_id' => $sub1->id,
                'description' => 'Lorem ipsum dolor sit amet, consecte adipiscing elit, sed do eiusmod teincididunt ut labore et dolore magna aliqua.',
            ]);
            GraphDescription::create([
                'graph_type' => 'online',
                'sub_category_id' => $sub2->id,
                'description' => 'Lorem ipsum dolor sit amet, consecte adipiscing elit, sed do eiusmod teincididunt ut labore et dolore magna aliqua.',
            ]);
        } else if ($request->input('form_section_id') == 2) {
            Question_category::create([
                'form_section_id' => 2,
                'name' => $request->input('name'),
                'description' => null,
            ]);
        }

        return redirect()->route('admin.edit-module-questions');
    }

    public function deleteCategory($id): RedirectResponse
    {
        Question::where('question_category_id', $id)->delete();
        $category = Question_category::find($id);
        $category->delete();

        return redirect()->route('admin.edit-module-questions');
    }

    public function updateAnswer(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required'],
            'description' => ['nullable'],
        ]);

        Module_level_answer::where('answer', $request->input('old_answer'))->update([
            'answer' => $request->input('text'),
            'description' => $request->input('description')
        ]);

        return redirect()->route('admin.edit-module-questions');
    }
}
