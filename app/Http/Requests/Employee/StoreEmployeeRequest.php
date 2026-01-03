<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // later: policies
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'age'    => 'required|integer|min:18|max:65',
            'job'    => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
        ];
    }
}
