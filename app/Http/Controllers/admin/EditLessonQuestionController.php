<?php

namespace App\Http\Controllers\admin;

use App\Models\Content;
use App\Models\Form_section;
use App\Models\Question_category;
use App\Models\Question;
use App\Models\Sub_category;
use App\Models\GraphDescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditLessonQuestionController
{
    public function index(): View
    {
        $lessonCategories = Question_category::where('form_section_id', 1)->get();
        $lessonSubCategories = Sub_category::all();
        $lessonQuestions = collect();
        $formSections = Form_section::all();

        foreach ($lessonCategories as $cat) {
            $questions = Question::where("question_category_id", $cat->id)->get();
            $lessonQuestions = $lessonQuestions->merge($questions);
        }
        return view(
            'admin.edit-lesson-questions',
            [
                'lessonCategories' => $lessonCategories,
                'lessonSubCategories' => $lessonSubCategories,
                'lessonQuestions' => $lessonQuestions,
                'formSections' => $formSections
            ]
        );
    }

    public function updateQuestion(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required'],
            'description' => ['nullable'],
        ]);

        Question::where('id', $request->question_id)->update([
            'text' => $request->input('text'),
            'description' => $request->input('description'),
        ]);
        return redirect()->route('admin.edit-lesson-questions');
    }

    public function createQuestion(Request $request): RedirectResponse
    {
        $request->validate([
            'text' => ['required'],
            'description' => ['nullable'],
        ]);

        Question::insert([
            'question_category_id' => $request->input('question_category_id'),
            'sub_category_id' => $request->input('sub_category_id'),
            'text' => $request->input('text'),
            'description' => $request->input('description'),
        ]);
        return redirect()->route('admin.edit-lesson-questions');
    }

    public function deleteQuestion($id): RedirectResponse
    {
        Question::where('id', $id)->delete();
        return redirect()->route('admin.edit-lesson-questions');
    }

    public function updateCategory(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required'],
            'form_section_id' => ['required'],
        ]);
        $toUpdate = Sub_category::where('name', $request->input('category_name'))->get();
        $firstSubCat = $toUpdate->first();

        $cat = Question_category::find($firstSubCat->question_category_id);

        if ($firstSubCat && (string)$request->input('form_section_id') !== (string)$cat->form_section_id) {
            Question_category::create([
                'form_section_id' => 2,
                'name' => $request->input('name'),
                'description' => null,
            ]);

            foreach ($toUpdate as $subCat) {
                Question::where('sub_category_id', $subCat->id)->delete();
                $subCat->delete();
            }
        } else {
            Sub_category::where('name', $request->input('category_name'))->update([
                'name' => $request->input('name'),
            ]);
        }

        return redirect()->route('admin.edit-lesson-questions');
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


        return redirect()->route('admin.edit-lesson-questions');
    }

    public function deleteCategory($id): RedirectResponse
    {
        $subCatName = Sub_category::find($id);

        $toDelete = Sub_category::where('name', $subCatName->name)->get();

        foreach ($toDelete as $subCat) {
            Question::where('sub_category_id', $subCat->id)->delete();
            $subCat->delete();
        }

        return redirect()->route('admin.edit-lesson-questions');
    }
}
