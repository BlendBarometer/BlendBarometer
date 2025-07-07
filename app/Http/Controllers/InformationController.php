<?php

namespace App\Http\Controllers;

use App\Models\Academy;
use App\Models\Content;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function view()
    {
        $academies = Academy::all();
        $intermediate = Content::where('section_name', 'intermediate_information')->firstOrFail();
        $previous = $intermediate->show ? route('intermediate.view', 'gegevens') : route('home');
        return view('information', [
            'academies' => $academies,
            'previous' => $previous,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'summary' => 'max:2000',
        ], [
            'summary.max' => 'De samenvatting mag niet langer zijn dan 2000 tekens.',
        ]);
        session()->put('name', request('name'));
        session()->put('course', request('course'));
        session()->put('academy', request('academy'));
        session()->put('academy-abbreviation', Academy::where('name', request('academy'))->value('abbreviation'));
        session()->put('module', request('module'));
        session()->put('summary', request('summary'));

        return redirect(route('intermediate.view', 'lesniveau'));
    }
}
