<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademyRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->hasRole('admin'); }

    public function rules(): array
    {
        $current = mb_strtolower($this->academy->abbreviation, 'UTF8');
    
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('academy', 'name')->ignore($this->academy->name,'name'),
            ],
            'abbreviation' => [
                'required','string','max:10',
                function ($attribute, $value, $fail) use ($current) {
                    $valueLower = mb_strtolower($value, 'UTF8');
    
                    if ($valueLower !== $current) {
                        $exists = \App\Models\Academy::query()
                            ->whereRaw('lower(abbreviation) = ?', [$valueLower])
                            ->exists();
    
                        if ($exists) {
                            $fail('Deze afkorting bestaat al (hoofdletterongevoelig).');
                        }
                    }
                },
            ],
        ];
    }
    
}