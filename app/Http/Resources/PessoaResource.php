<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PessoaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pessoa_id' => $this->id,
            'nome' => $this->nome,
            'data_nascimento' => $this->data_nascimento,
            'sexo' => $this->sexo,
            'mae' => $this->mae,
            'pai' => $this->pai,
            'foto' => new FotoPessoaResource($this->whenLoaded('foto')),
            'endereco' => new EnderecoResource($this->whenLoaded('endereco')->first()),
        ];
    }
}
