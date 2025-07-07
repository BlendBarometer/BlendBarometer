<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ConfirmationController extends Controller
{
    public function view(): View
    {
        return view('confirmation', [
            'ictoCoach' => session('success.ictoCoach'),
            'academy' => session('success.academy'),
            'course' => session('success.course'),
            'module' => session('success.module'),
            'teacher' => session('success.teacher'),
            'date' => session('success.date'),
        ]);
    }
}
