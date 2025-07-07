<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // admin middleware already checked
    }

    public function rules(): array
    {
        return [
            'academy_name' => ['nullable', 'string', 'exists:academy,name'],
            'email' => ['required', 'email'],
        ];
    }
}
