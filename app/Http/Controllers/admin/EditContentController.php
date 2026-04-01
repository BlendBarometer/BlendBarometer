<?php

namespace App\Http\Controllers\admin;

use App\Models\Content;
use App\Models\Sub_category;
use App\Models\GraphDescription;
use App\Models\Graph_legenda;
use App\Models\ModuleInformationField;
use App\Models\Module_level_answer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EditContentController
{
    public function index(): View
    {
        $home = Content::where('section_name', 'intro_description')->value('info');

        $lessonLevelPhysicalSubcategories = Sub_category::select('sub_category.name', "sub_category.id", 'graph_description.description')
            ->leftJoin('graph_description', function ($join) {
                $join->on('sub_category.id', '=', 'graph_description.sub_category_id')
                    ->where('graph_description.graph_type', '=', 'physical');
            })
            ->where('sub_category.question_category_id', 1)
            ->get();

        $lessonLevelOnlineSubcategories = Sub_category::select('sub_category.name', "sub_category.id", 'graph_description.description')
            ->leftJoin('graph_description', function ($join) {
                $join->on('sub_category.id', '=', 'graph_description.sub_category_id')
                    ->where('graph_description.graph_type', '=', 'online');
            })
            ->where('sub_category.question_category_id', 2)
            ->get();

        $generalLessonLevelDescription = GraphDescription::select('id', 'description')->where('graph_type', 'lesson-level-general')->first();

        $generalModuleDescription = GraphDescription::select('id', 'description')->where('graph_type', 'module-level-general')->first();

        $intermediateContent = [
            "information" => Content::where('section_name', 'intermediate_information')->select('info', 'show')->first(),
            "lesson" => Content::where('section_name', 'intermediate_lesson')->select('info', 'show')->first(),
            "module" => Content::where('section_name', 'intermediate_module')->select('info', 'show')->first(),
            "results" => Content::where('section_name', 'intermediate_results')->select('info', 'show')->first(),
        ];

        $legenda = Graph_legenda::all();
        $moduleLevelAnswers = Module_level_answer::all();
        $moduleInformationFields = $this->getModuleInformationFields();

        try {
            $tab = request()->get('tab', 'home');
        } catch (\Exception $e) {
            $tab = 'home';
        }

        return view(
            'admin.edit-content',
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
                'moduleInformationFields' => $moduleInformationFields,
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
        foreach ($descriptions as $description) {
            $this->UpdateChartDescription($description);
        }

        $descriptions = $request->input('online');
        foreach ($descriptions as $description) {
            $this->UpdateChartDescription($description);
        }

        $generalLessonLevelDescription = $request->input('general_lesson_level');
        $description = GraphDescription::find($generalLessonLevelDescription['id']);
        if ($description) {
            $description->update(['description' => $generalLessonLevelDescription['description']]);
        }

        $generalModuleDescription = $request->input('general_module');
        $description = GraphDescription::find($generalModuleDescription['id']);
        if ($description) {
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
            if ($category) {
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

    public function updateModuleInformationFields(Request $request): RedirectResponse
    {
        if (!Schema::hasTable('module_information_field')) {
            return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
        }

        $request->validate([
            'fields' => ['required', 'array'],
            'fields.*.title' => ['required', 'string', 'max:255'],
            'fields.*.placeholder' => ['nullable', 'string'],
            'fields.*.maxlength' => ['required', 'integer', 'min:1', 'max:2000'],
            'fields.*.sort_order' => ['required', 'integer', 'min:0'],
            'fields.*.is_active' => ['required', 'string', 'in:true,false'],
        ]);

        foreach ($request->input('fields', []) as $id => $data) {
            ModuleInformationField::where('id', $id)->update([
                'title' => $data['title'],
                'placeholder' => $data['placeholder'] ?? null,
                'maxlength' => $data['maxlength'],
                'sort_order' => $data['sort_order'],
                'is_active' => ($data['is_active'] ?? 'false') === 'true',
            ]);
        }

        return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
    }

    public function createModuleInformationField(): RedirectResponse
    {
        if (!Schema::hasTable('module_information_field')) {
            return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
        }

        $fieldCount = ModuleInformationField::query()->count();
        $nextNumber = $fieldCount + 1;
        $nextSortOrder = (int) (ModuleInformationField::query()->max('sort_order') ?? 0) + 1;

        ModuleInformationField::query()->create([
            'key' => $this->generateModuleInformationFieldKey($nextNumber),
            'title' => "Nieuwe vraag $nextNumber",
            'placeholder' => 'Beschrijf hier wat de invuller moet beantwoorden.',
            'maxlength' => 2000,
            'sort_order' => $nextSortOrder,
            'is_active' => false,
        ]);

        return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
    }

    public function deleteModuleInformationField(ModuleInformationField $field): RedirectResponse
    {
        if (!Schema::hasTable('module_information_field')) {
            return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
        }

        $field->delete();

        return redirect()->route('admin.edit-content', ['tab' => 'module-information']);
    }

    private function getModuleInformationFields(): Collection
    {
        if (!Schema::hasTable('module_information_field')) {
            return collect();
        }

        return ModuleInformationField::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function generateModuleInformationFieldKey(int $nextNumber): string
    {
        $base = "custom-field-$nextNumber";
        $key = $base;
        $suffix = 1;

        while (ModuleInformationField::query()->where('key', $key)->exists()) {
            $key = "$base-$suffix";
            $suffix++;
        }

        return $key;
    }
}
