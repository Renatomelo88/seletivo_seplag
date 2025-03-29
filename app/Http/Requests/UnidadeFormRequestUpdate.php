<?php

namespace App\Http\Requests;

use App\Models\Unidade;
use RenatoMelo\Rule\Iunique;

class UnidadeFormRequestUpdate extends FormRequestBase
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
            'nome' => [
                'sometimes',
                'string',
                'max:200',
                new Iunique(Unidade::class, $this->unidade),
            ],
            'sigla' => [
                'sometimes',
                'string',
                'max:200',
                new Iunique(Unidade::class, $this->unidade),
            ],
            'tipo_logradouro' => 'sometimes|string|max:50',
            'logradouro' => 'sometimes|string|max:200',
            'numero' => 'sometimes|integer',
            'bairro' => 'sometimes|string|max:100',
            'cidade' => 'sometimes|string|max:200',
            'uf' => 'sometimes|string|size:2',
        ];
    }

    public function messages(): array
    {
        return [];
    }

}
