<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAcademyRequest;
use App\Http\Requests\Admin\UpdateAcademyRequest;
use App\Models\Academy;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AcademyController extends Controller
{
    /** List + search + paging */
    public function index(Request $request): View
    {
        $query = Academy::query()
            ->orderBy('abbreviation');

        if ($search = $request->input('q')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('abbreviation', 'like', "%{$search}%"));
        }

        $academies = $query->paginate(10)->withQueryString();

        return view('admin.academies.index', compact('academies', 'search'));
    }

    public function create(): View
    {
        return view('admin.academies.form', ['academy' => new Academy()]);
    }

    public function store(StoreAcademyRequest $request): RedirectResponse
    {
        Academy::create($request->validated());

        return redirect()
            ->route('admin.academies.index')
            ->with('success', 'Academie toegevoegd.');
    }

    public function edit(Academy $academy): View
    {
        return view('admin.academies.form', compact('academy'));
    }

    public function update(UpdateAcademyRequest $request, Academy $academy): RedirectResponse
    {
        $academy->update($request->validated());

        return redirect()
            ->route('admin.academies.index')
            ->with('success', 'Academie bijgewerkt.');
    }

    public function destroy(Academy $academy): RedirectResponse
    {
        $academy->delete();

        return back()->with('success', 'Academie verwijderd.');
    }
}
