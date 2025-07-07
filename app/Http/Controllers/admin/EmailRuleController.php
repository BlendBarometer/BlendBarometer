<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmailRuleRequest;
use App\Models\Academy;
use App\Models\EmailRule;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EmailRuleController extends Controller
{
    public function index(): View
    {
        // grouped e-mailrules
        $rules = EmailRule::query()
            ->orderBy('academy_name')
            ->orderBy('email')
            ->get()
            ->groupBy(fn (EmailRule $r) => $r->academy_name ?? '_default')
            ->toBase();

        // all academies
        $allAcademies = Academy::query()
            ->orderBy('abbreviation')
            ->get(['name', 'abbreviation']);

        // which academies already have a rule?
        $usedNames = $rules->keys()->filter(fn ($k) => $k !== '_default');

        // available academies
        $availableAcademies = $allAcademies->whereNotIn('name', $usedNames->all());

        return view('admin.email-rules', [
            'rules' => $rules,
            'allAcademies' => $allAcademies,
            'usedNames' => $usedNames,
            'availableAcademies' => $availableAcademies,
            'success' => session('success'),
        ]);
    }

    public function store(StoreEmailRuleRequest $request): RedirectResponse
    {
        $email = $request->validated('email');
        $academyName = $request->validated('academy_name');

        try {
            EmailRule::create([
                'academy_name' => $academyName,
                'email' => $email,
            ]);
        } catch (QueryException $e) {
            throw ValidationException::withMessages([
                'email' => 'Dit adres bestaat al voor deze academie.',
            ]);
        }

        return back()->with('success', 'E-mailadres toegevoegd.');
    }

    public function changeAcademy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'old_academy' => ['nullable', 'string'],
            'academy_name' => ['required', 'string', 'different:old_academy'],
        ]);

        // prevent double rules
        if (EmailRule::where('academy_name', $data['academy_name'])->exists()) {
            return back()->withErrors([
                'academy_name' => 'Er bestaat al een regel voor deze academie.',
            ]);
        }

        $fromAcademy = $data['old_academy']; // can be null
        $toAcademy = $data['academy_name'];

        // retrieve all 'old' academy emails
        $emails = EmailRule::where('academy_name', $fromAcademy)->pluck('email');

        // add to new academy (duplica­tion-safe)
        foreach ($emails as $email) {
            EmailRule::firstOrCreate([
                'academy_name' => $toAcademy,
                'email' => $email,
            ]);
        }

        // delete old rules
        EmailRule::where('academy_name', $fromAcademy)->delete();

        return back()->with('success', 'Academie aangepast.');
    }

    public function destroy(EmailRule $rule): RedirectResponse
    {
        $rule->delete();

        return back()->with('success', 'E-mailadres verwijderd.');
    }
}
