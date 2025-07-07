<?php

namespace App\Http\Controllers\admin;

use App\Models\Content;
use App\Models\Sub_category;
use App\Models\GraphDescription;
use App\Models\Graph_legenda;
use App\Models\Module_level_answer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Pail\ValueObjects\Origin\Console;

class EditContentController
{
    public function index(): View
    {
        $home = Content::where('section_name', 'intro_description')->value('info');

        $lessonLevelPhysicalSubcategories = Sub_category::select('sub_category.name',"sub_category.id", 'graph_description.description')
            ->leftJoin('graph_description', function($join) {
                $join->on('sub_category.id', '=', 'graph_description.sub_category_id')
             ->where('graph_description.graph_type', '=', 'physical');
        })
        ->where('sub_category.question_category_id', 1)
        ->get();

        $lessonLevelOnlineSubcategories = Sub_category::select('sub_category.name',"sub_category.id", 'graph_description.description')
            ->leftJoin('graph_description', function($join) {
                $join->on('sub_category.id', '=', 'graph_description.sub_category_id')
             ->where('graph_description.graph_type', '=', 'online');
        })
        ->where('sub_category.question_category_id', 2)
        ->get();
        
        $generalLessonLevelDescription = GraphDescription::select('id','description')->where('graph_type','lesson-level-general')->first();

        $generalModuleDescription = GraphDescription::select('id','description')->where('graph_type','module-level-general')->first();

        $intermediateContent = [
            "information" => Content::where('section_name', 'intermediate_information')->select('info', 'show')->first(),
            "lesson" => Content::where('section_name', 'intermediate_lesson')->select('info', 'show')->first(),
            "module" => Content::where('section_name', 'intermediate_module')->select('info', 'show')->first(),
            "results" => Content::where('section_name', 'intermediate_results')->select('info', 'show')->first(),
        ];

        $legenda = Graph_legenda::all();
        $moduleLevelAnswers = Module_level_answer::all();

        try {
            $tab = request()->get('tab', 'home');
        } catch (\Exception $e) {
            $tab = 'home';
        }

        return view('admin.edit-content',
            [
                'tab' => $tab,
                'home' => $home,
                'intermediateContent' => $intermediateContent,
                'lessonLevelPhysicalSubcategories' => $lessonLevelPhysicalSubcategories, 
                'lessonLevelOnlineSubcategories' => $lessonLevelOnlineSubcategories,
                'generalLessonLevelDescription' => $generalLessonLevelDescription,
                'generalModuleDescription' => $generalModuleDescription,
                'legenda' => $legenda,
                'moduleLevelAnswers' => $moduleLevelAnswers,
            ]
        );
    }

    public function updateHomeContent(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => ['required'],
        ]);

        Content::where('section_name', 'intro_description')->update(['info' => $request->input('content')]);
        return redirect()->route('admin.edit-content');
    }

    public function updateChartContent(Request $request)
    {
        $descriptions = $request->input('physical');
        foreach($descriptions as $description){
            $this->UpdateChartDescription($description);
        }

        $descriptions = $request->input('online');
        foreach($descriptions as $description){
            $this->UpdateChartDescription($description);
        }

        $generalLessonLevelDescription = $request->input('general_lesson_level');
        $description = GraphDescription::find($generalLessonLevelDescription['id']);
        if($description)
        {
            $description->update(['description' => $generalLessonLevelDescription['description']]);
        }

        $generalModuleDescription = $request->input('general_module');
        $description = GraphDescription::find($generalModuleDescription['id']);
        if($description)
        {
            $description->update(['description' => $generalModuleDescription['description']]);
        }
        return redirect()->route('admin.edit-content', ['tab' => 'chart']);
    }

    private function UpdateChartDescription($description)
    {
        $row = GraphDescription::where('sub_category_id', $description['id'])->first();

        if ($row) {
            $row->update(['description' => $description['description']]);
        } else {
            $category = Sub_category::find($description['id']);
            if($category){
                            GraphDescription::create([
                'sub_category_id' => $description['id'],
                'graph_type' => $category->question_category_id == 1 ? 'physical' : 'online',
                'description' => $description['description'],
            ]);
            }
        }
    }   
    public function updateIntermediateContent(Request $request, string $sectionName): RedirectResponse
    {
        $request->validate([
            'content' => ['required'],
            'show' => ['required', 'string', 'in:true,false'],
        ]);

        Content::where('section_name', 'intermediate_' . $sectionName)
            ->update([
                'info' => $request->input('content'),
                'show' => $request->input('show') === 'true',
            ]);
        return redirect()->route('admin.edit-content', ['tab' => $sectionName]);
    }

    public function updateLegenda(Request $request)
    {
        $request->validate([
            'legenda.*.color' => ['required'],
            'legenda.*.name' => ['required'],
            'legenda.*.description' => ['required'],
        ]);
        foreach ($request->input('legenda', []) as $id => $row) {
            Graph_legenda::where('id', $id)->update([
                'color' => $row['color'],
                'name' => $row['name'],
                'description' => $row['description'],
            ]);
        }
        return redirect()->route('admin.edit-content', ['tab' => 'legenda']);
    }
}
