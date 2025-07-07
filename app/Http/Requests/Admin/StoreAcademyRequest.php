<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademyRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        return [
            'name'         => ['required','string','max:255','unique:academy,name'],
            'abbreviation' => [
                'required','string','max:10',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\Academy::query()
                        ->whereRaw('lower(abbreviation) = ?', [mb_strtolower($value, 'UTF8')])
                        ->exists();
    
                    if ($exists) {
                        $fail('Deze afkorting bestaat al (hoofdletterongevoelig).');
                    }
                },
            ],
        ];
    }
}
