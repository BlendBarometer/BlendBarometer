<?php

use App\Http\Controllers\admin\AuthController as AdminAuthController;
use App\Http\Controllers\admin\EditContentController;
use App\Http\Controllers\admin\EditLessonQuestionController;
use App\Http\Controllers\admin\EditModuleQuestionController;
use App\Http\Controllers\admin\EmailRuleController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfirmationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\IntermediateController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultsController;
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Authenticate_admin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/inloggen', [AuthController::class, 'login'])->name('login');
Route::post('/inloggen', [AuthController::class, 'submitLogin'])->name('login.submit');
Route::get('/verificatie', [AuthController::class, 'verify'])->name('verify');
Route::post('verificatie', [AuthController::class, 'submitVerify'])->name('verify.submit');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/gegevens', [InformationController::class, 'view'])->name('information');
    Route::post('/gegevens', [InformationController::class, 'submit'])->name('information.submit');

    Route::get('/moduleniveau/{categoryNr}', [ModuleController::class, 'getModuleLevel'])->name('module-level');
    Route::post('/moduleniveau/{categoryNr}/versturen', [ModuleController::class, 'submit'])->name('module-level.submit');
    Route::get('/moduleniveau/{categoryNr}/volgende', [ModuleController::class, 'next'])->name('module-level.next');
    Route::get('/moduleniveau/{categoryNr}/vorige', [ModuleController::class, 'previous'])->name('module-level.previous');

    Route::get('/lesniveau/{id}', [LessonController::class, 'view'])->name('lesson-level');
    Route::post('/lesniveau/{id}/versturen', [LessonController::class, 'submit'])->name('lesson-level.submit');
    Route::get('/lesniveau/volgende/{id}', [LessonController::class, 'next'])->name('lesson-level.next');
    Route::get('/lesniveau/vorige/{id}', [LessonController::class, 'previous'])->name('lesson-level.previous');
    Route::get('/lesniveau/{id}/delete/{questionId}', [LessonController::class, 'delete'])->name('lesson-level.delete');

    Route::get('/uitleg-overzicht-en-resultaten', [ResultsController::class, 'overviewAndResultsInfoView'])->name('overview-and-results-info');
    Route::get('/resultaten', [ResultsController::class, 'view'])->name('results');
    Route::get('/overzicht-en-versturen', [ResultsController::class, 'overviewAndSendView'])->name('overview-and-send');

    Route::get('/versturen', [ReportController::class, 'sendReport'])->name('send');
    Route::get('/tussenpagina/{sectionName}', [IntermediateController::class, 'view'])->name('intermediate.view');
});

Route::get('/bevestiging', [ConfirmationController::class, 'view'])->name('confirmation');
Route::post('/SaveChart', [ResultsController::class, 'saveChart']);

Route::get('/admin', [AdminAuthController::class, 'index'])->name('admin.login');
Route::post('/admin', [AdminAuthController::class, 'submitLogin'])->name('admin.submit');

Route::middleware([Authenticate_admin::class])->name('admin.')->prefix('admin')->group(function () {
    Route::get('/uitloggen', [AdminAuthController::class, 'logout'])->name('logout');

    Route::prefix('academies')
        ->name('academies.')
        ->controller(\App\Http\Controllers\admin\AcademyController::class)
        ->group(function () {
            Route::get('/',        'index' )->name('index');
            Route::get('create',   'create')->name('create');
            Route::post('/',       'store' )->name('store');
            Route::get('{academy}/edit', 'edit'  )->name('edit');
            Route::put('{academy}',       'update')->name('update');
            Route::delete('{academy}',    'destroy')->name('destroy');
        });

    Route::prefix('email-rules')
         ->name('email-rules.')
         ->controller(EmailRuleController::class)
         ->group(function () {
            Route::get('/',           'index'        )->name('index');
            Route::post('/',          'store'        )->name('store');
            Route::patch('/academy',  'changeAcademy')->name('change');
            Route::delete('{rule}',   'destroy'      )->name('destroy');
         });

    Route::get('/vragen-bewerken/lesniveau', [EditLessonQuestionController::class, 'index'])->name('edit-lesson-questions');
    Route::put('/vragen-bewerken/lesniveau/{question}/update', [EditLessonQuestionController::class, 'updateQuestion'])->name('edit-lesson-questions.update');
    Route::post('/vragen-bewerken/lesniveau/create', [EditLessonQuestionController::class, 'createQuestion'])->name('edit-lesson-questions.create');
    Route::delete('/vragen-bewerken/lesniveau/{question}/verwijder', [EditLessonQuestionController::class, 'deleteQuestion'])->name('edit-lesson-questions.delete');

    route::put('/vragen-bewerken/lesniveau/categorie-bewerken/{categorie}/update', [EditLessonQuestionController::class, 'updateCategory'])->name('edit-lesson-questions.edit-categorie.update');
    Route::post('/vragen-bewerken/lesniveau/categorie-bewerken/create', [EditLessonQuestionController::class, 'createCategory'])->name('edit-lesson-questions.edit-category.create');
    Route::delete('/vragen-bewerken/lesniveau/categorie-bewerken/{categorie}/verwijder', [EditLessonQuestionController::class, 'deleteCategory'])->name('edit-lesson-questions.edit-categorie.delete');

    Route::get('/vragen-bewerken/moduleniveau', [EditModuleQuestionController::class, 'index'])->name('edit-module-questions');
    Route::put('/vragen-bewerken/moduleniveau/{question}/update', [EditModuleQuestionController::class, 'updateQuestion'])->name('edit-module-questions.update');
    Route::post('/vragen-bewerken/moduleniveau/create', [EditModuleQuestionController::class, 'createQuestion'])->name('edit-module-questions.create');
    Route::delete('/vragen-bewerken/moduleniveau/{question}/verwijder', [EditModuleQuestionController::class, 'deleteQuestion'])->name('edit-module-questions.delete');

    route::put('/vragen-bewerken/moduleniveau/categorie-bewerken/{categorie}/update', [EditModuleQuestionController::class, 'updateCategory'])->name('edit-module-questions.edit-categorie.update');
    Route::post('/vragen-bewerken/moduleniveau/categorie-bewerken/create', [EditModuleQuestionController::class, 'createCategory'])->name('edit-module-questions.edit-category.create');
    Route::delete('/vragen-bewerken/moduleniveau/categorie-bewerken/{categorie}/verwijder', [EditModuleQuestionController::class, 'deleteCategory'])->name('edit-module-questions.edit-categorie.delete');

    route::put('/vragen-bewerken/moduleniveau/antwoord-bewerken/{antwoord}/update', [EditModuleQuestionController::class, 'updateAnswer'])->name('edit-module-questions.edit-answer.update');

    Route::get('/content-bewerken', [EditContentController::class, 'index'])->name('edit-content');
    
    Route::put('/content-bewerken/homepagina-opslaan', [EditContentController::class, 'updateHomeContent'])->name('edit-content.home-update');

    Route::put('/content-bewerken/grafiekomschrijving-opslaan', [EditContentController::class, 'updateChartContent'])->name('edit-content.chart-update');
    Route::put('/content-bewerken/tussenpagina-opslaan/{section}', [EditContentController::class, 'updateIntermediateContent'])->name('edit-content.intermediate-update');

    Route::put('/content-bewerken/legenda-opslaan', [EditContentController::class, 'updateLegenda'])->name('edit-content.legenda-update');
});
