<?php

namespace Tests\Feature;

use App\Http\Controllers\InformationController;
use App\Models\Academy;
use App\Models\Content;
use App\Models\ModuleInformationAnswer;
use App\Models\ModuleInformationField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class InformationModuleFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_information_view_renders_module_information_from_database(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        Content::create([
            'section_name' => 'intermediate_information',
            'info' => '<p>Intro</p>',
            'show' => true,
        ]);

        Academy::create([
            'name' => 'Academie Test',
            'abbreviation' => 'AT',
        ]);

        $field = ModuleInformationField::create([
            'key' => 'summary',
            'title' => 'Eigen samenvatting',
            'placeholder' => 'Schrijf hier je aangepaste samenvatting',
            'maxlength' => 1234,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        ModuleInformationAnswer::create([
            'user_id' => $user->id,
            'module_information_field_id' => $field->id,
            'answer' => 'Bestaand antwoord',
        ]);

        view()->share('errors', new ViewErrorBag());

        $view = app(InformationController::class)->view();
        $html = $view->render();

        $this->assertStringContainsString('Eigen samenvatting', $html);
        $this->assertStringContainsString('Schrijf hier je aangepaste samenvatting', $html);
        $this->assertStringContainsString('Bestaand antwoord', $html);
    }

    public function test_information_submit_persists_module_information_answers_per_user(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        Academy::create([
            'name' => 'Academie Test',
            'abbreviation' => 'AT',
        ]);

        $summaryField = ModuleInformationField::create([
            'key' => 'summary',
            'title' => 'Samenvatting',
            'placeholder' => 'Samenvatting',
            'maxlength' => 2000,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $goalsField = ModuleInformationField::create([
            'key' => 'goals',
            'title' => 'Leeruitkomsten',
            'placeholder' => 'Leeruitkomsten',
            'maxlength' => 2000,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $evaluationField = ModuleInformationField::create([
            'key' => 'evaluation',
            'title' => 'Toetsing',
            'placeholder' => 'Toetsing',
            'maxlength' => 2000,
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $request = Request::create('/gegevens', 'POST', [
            'name' => 'Docent Test',
            'course' => 'Software Ontwikkeling',
            'academy' => 'Academie Test',
            'module' => 'Programmeren',
            'summary' => 'Dit is een samenvatting',
            'goals' => 'Dit zijn leeruitkomsten',
            'evaluation' => 'Dit is de toetsing',
        ]);

        $this->app->instance('request', $request);
        $response = app(InformationController::class)->submit($request);

        $this->assertSame(route('intermediate.view', 'lesniveau'), $response->getTargetUrl());

        $this->assertDatabaseHas('module_information_answer', [
            'user_id' => $user->id,
            'module_information_field_id' => $summaryField->id,
            'answer' => 'Dit is een samenvatting',
        ]);

        $this->assertDatabaseHas('module_information_answer', [
            'user_id' => $user->id,
            'module_information_field_id' => $goalsField->id,
            'answer' => 'Dit zijn leeruitkomsten',
        ]);

        $this->assertDatabaseHas('module_information_answer', [
            'user_id' => $user->id,
            'module_information_field_id' => $evaluationField->id,
            'answer' => 'Dit is de toetsing',
        ]);
    }
}
