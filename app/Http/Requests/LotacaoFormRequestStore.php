<?php

namespace App\Http\Requests;

use App\Models\Unidade;
use Illuminate\Validation\Rule;
use RenatoMelo\Rule\Iunique;

class LotacaoFormRequestStore extends FormRequestBase
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
            'pessoa_id' => [
                'required',
                'integer',
                Rule::exists('pessoa', 'id')->whereNull('deleted_at'),
                Rule::unique('lotacao', 'pessoa_id')
                    ->whereNull('data_remocao')
                    ->whereNull('deleted_at')
                    ->where('pessoa_id', $this->input('pessoa_id')),
            ],
            'unidade_id' => [
                'required',
                'integer',
                Rule::exists('unidade', 'id')->whereNull('deleted_at'),
            ],
            'data_lotacao' => 'required|date_format:Y-m-d',
            'data_remocao' => 'nullable|date_format:Y-m-d|after:data_lotacao',
            'portaria' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'pessoa_id.exists' => 'Não existe uma pessoa com esse id.',
            'unidade_id.exists' => 'Não existe uma unidade com esse id.',
            'pessoa_id.unique' => 'Já existe uma lotação ativa para essa pessoa. Uma pessoa não pode ter mais de uma lotação ativa ao mesmo tempo.',
        ];
    }

}
