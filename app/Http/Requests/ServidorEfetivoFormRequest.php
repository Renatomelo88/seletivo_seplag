<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ServidorEfetivoFormRequest extends FormRequestBase
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
            'matricula' => [
                'required',
                'string',
                'max:20',
                Rule::unique('servidor_efetivo', 'matricula')->whereNull('deleted_at'),
            ],
            'nome' => [
                'required',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'data_nascimento' => 'required|date_format:Y-m-d',
            'sexo' => 'required',
            'mae' => [
                'required',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'pai' => [
                'required',
                'string',
                'max:200',
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'foto' => 'required|max:2048',
            'tipo_logradouro' => 'required|string|max:50',
            'logradouro' => 'required|string|max:200',
            'numero' => 'required|integer',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:200',
            'uf' => 'required|string|max:2',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.regex' => 'O nome deve conter nome e sobrenome e não pode ter números.',
            'mae.regex' => 'O nome da mãe deve conter nome e sobrenome e não pode ter números.',
            'pai.regex' => 'O nome do pai deve conter nome e sobrenome e não pode ter números.',
            'matricula.unique' => 'A matrícula informada já está em uso.',
        ];
    }

}
