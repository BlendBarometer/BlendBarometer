<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use App\Models\Content;
use App\Models\ModuleInformationAnswer;
use App\Models\ModuleInformationField;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class InformationController extends Controller
{
    private const MODULE_INFORMATION_DEFAULT_MAX_LENGTH = 2000;

    public function view()
    {
        $academies = Academy::all();
        $intermediate = Content::where('section_name', 'intermediate_information')->firstOrFail();
        $previous = $intermediate->show ? route('intermediate.view', 'gegevens') : route('home');

        $moduleInformationFields = $this->getModuleInformationFields();
        $moduleInformationAnswers = [];

        if (Auth::check() && Schema::hasTable('module_information_answer')) {
            $moduleInformationAnswers = ModuleInformationAnswer::query()
                ->where('user_id', Auth::id())
                ->whereIn('module_information_field_id', $moduleInformationFields->pluck('id')->filter())
                ->pluck('answer', 'module_information_field_id')
                ->toArray();
        }

        return view('information', [
            'academies' => $academies,
            'previous' => $previous,
            'moduleInformationFields' => $moduleInformationFields,
            'moduleInformationAnswers' => $moduleInformationAnswers,
        ]);
    }

    public function submit(Request $request)
    {
        $moduleInformationFields = $this->getModuleInformationFields();

        $rules = [];
        $messages = [];

        foreach ($moduleInformationFields as $field) {
            $maxlength = (int) ($field->maxlength ?? self::MODULE_INFORMATION_DEFAULT_MAX_LENGTH);
            $maxlength = min($maxlength, self::MODULE_INFORMATION_DEFAULT_MAX_LENGTH);

            $rules[$field->key] = ["nullable", "max:{$maxlength}"];
            $messages["{$field->key}.max"] = "De {$field->title} mag niet langer zijn dan {$maxlength} tekens.";
        }

        $request->validate($rules, $messages);

        session()->put('name', $request->input('name'));
        session()->put('course', $request->input('course'));
        session()->put('academy', $request->input('academy'));
        session()->put('academy-abbreviation', Academy::where('name', $request->input('academy'))->value('abbreviation'));
        session()->put('module', $request->input('module'));

        foreach ($moduleInformationFields as $field) {
            $answer = (string) $request->input($field->key, '');
            session()->put($field->key, $answer);

            if ($field->id !== null && Auth::check() && Schema::hasTable('module_information_answer')) {
                ModuleInformationAnswer::updateOrCreate(
                    [
                        'user_id' => Auth::id(),
                        'module_information_field_id' => $field->id,
                    ],
                    [
                        'answer' => $answer,
                    ]
                );
            }
        }

        return redirect(route('intermediate.view', 'lesniveau'));
    }

    private function getModuleInformationFields(): Collection
    {
        if (!Schema::hasTable('module_information_field')) {
            return $this->getDefaultModuleInformationFields();
        }

        $fields = ModuleInformationField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($fields->isEmpty()) {
            return $this->getDefaultModuleInformationFields();
        }

        return $fields;
    }

    private function getDefaultModuleInformationFields(): Collection
    {
        return collect([
            (object) [
                'id' => null,
                'key' => 'summary',
                'title' => 'Samenvatting',
                'placeholder' => 'Beschrijf in 5 zinnen waar deze module om gaat',
                'maxlength' => self::MODULE_INFORMATION_DEFAULT_MAX_LENGTH,
                'sort_order' => 1,
            ],
            (object) [
                'id' => null,
                'key' => 'goals',
                'title' => 'Leeruitkomsten',
                'placeholder' => 'Beschrijf in 5 zinnen wat de leeruitkomsten van deze module zijn',
                'maxlength' => self::MODULE_INFORMATION_DEFAULT_MAX_LENGTH,
                'sort_order' => 2,
            ],
            (object) [
                'id' => null,
                'key' => 'evaluation',
                'title' => 'Toetsing',
                'placeholder' => 'Beschrijf hoe de toetsing van deze module plaatsvindt',
                'maxlength' => self::MODULE_INFORMATION_DEFAULT_MAX_LENGTH,
                'sort_order' => 3,
            ],
        ]);
    }
}
