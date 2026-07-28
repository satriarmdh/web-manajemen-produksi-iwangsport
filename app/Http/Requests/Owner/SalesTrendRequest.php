<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;

class SalesTrendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'range' => ['nullable', 'in:7d,30d,1y'],
            'start' => ['nullable', 'date'],
            'end'   => ['nullable', 'date', 'after_or_equal:start'],
        ];
    }
}
