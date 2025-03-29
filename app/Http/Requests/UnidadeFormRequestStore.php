<?php

namespace App\Http\Requests;

use App\Models\Unidade;
use RenatoMelo\Rule\Iunique;

class UnidadeFormRequestStore extends FormRequestBase
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
                'required',
                'string',
                'max:200',
                new Iunique(Unidade::class),
            ],
            'sigla' => [
                'required',
                'string',
                'max:200',
                new Iunique(Unidade::class),
            ],
            'tipo_logradouro' => 'required|string|max:50',
            'logradouro' => 'required|string|max:200',
            'numero' => 'required|integer',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:200',
            'uf' => 'required|string|size:2',
        ];
    }

    public function messages(): array
    {
        return [];
    }

}
