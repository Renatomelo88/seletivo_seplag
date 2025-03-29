<?php

namespace App\Http\Requests;

use App\Models\Unidade;
use Illuminate\Validation\Rule;
use RenatoMelo\Rule\Iunique;

class LotacaoFormRequestUpdate extends FormRequestBase
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'data_lotacao' => 'sometimes|date_format:Y-m-d',
            'data_remocao' => 'nullable|date_format:Y-m-d|after:data_lotacao',
            'portaria' => 'sometimes|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [];
    }

}
