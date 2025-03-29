<?php

namespace App\Http\Requests;

use App\Models\ServidorTemporario;


class ServidorTemporarioFormRequestUpdate extends FormRequestBase
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {

        $servidor = ServidorTemporario::find($this->route('servidor_temporario'));

        if ($servidor && !$this->has('data_nascimento')) {
            // Se data_nascimento não estiver na requisição, usar o valor do banco
            $this->merge(['data_nascimento' => $servidor->pessoa->data_nascimento]);
        }

        if ($servidor && !$this->has('data_admissao')) {
            // Se data_admissao não estiver na requisição, usar o valor do banco
            $this->merge(['data_admissao' => $servidor->data_admissao]);
        }

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
                'regex:/^[A-Za-zÀ-ÿ]+(?: [A-Za-zÀ-ÿ]+)+$/',
            ],
            'data_admissao' => 'sometimes|date_format:Y-m-d|after:data_nascimento',
            'data_demissao' => 'sometimes|date_format:Y-m-d|after:data_admissao',
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
            'uf' => 'sometimes|string|size:2',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.regex' => 'O nome deve conter nome e sobrenome e não pode ter números.',
            'mae.regex' => 'O nome da mãe deve conter nome e sobrenome e não pode ter números.',
            'pai.regex' => 'O nome do pai deve conter nome e sobrenome e não pode ter números.',
        ];
    }

}
