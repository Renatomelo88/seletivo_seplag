<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ServidorEfetivoFormRequestUpdate extends FormRequestBase
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
        $matriculaOld = $this->route('servidor_efetivo');

        return [
            'matricula' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('servidor_efetivo', 'matricula')
                    ->ignore($matriculaOld, 'matricula')
                    ->whereNull('deleted_at'),
            ],
            'nome' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'data_nascimento' => 'sometimes|date_format:Y-m-d',
            'sexo' => 'sometimes',
            'mae' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'pai' => [
                'sometimes',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'foto' => 'sometimes|max:2048',
            'tipo_logradouro' => 'sometimes|string|max:50',
            'logradouro' => 'sometimes|string|max:200',
            'numero' => 'sometimes|integer',
            'bairro' => 'sometimes|string|max:100',
            'cidade' => 'sometimes|string|max:200',
            'uf' => 'sometimes|string|max:2',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.regex' => 'O nome deve conter nome e sobrenome e não pode ter números.',
            'mae.regex' => 'O nome da mãe deve conter nome e sobrenome e não pode ter números.',
            'pai.regex' => 'O nome do pai deve conter nome e sobrenome e não pode ter números.',
            'uf.required_with' => 'O UF é obrigatório quando a cidade é fornecida.',
            'matricula.unique' => 'A matrícula informada já está em uso.',
        ];
    }

}
