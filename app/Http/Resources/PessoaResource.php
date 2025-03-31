<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'idade' => $this->data_nascimento ? Carbon::parse($this->data_nascimento)->age : null,
            'sexo' => $this->sexo,
            'mae' => $this->mae,
            'pai' => $this->pai,
            'foto' => new FotoPessoaResource($this->whenLoaded('foto')),
            'endereco' => new EnderecoResource($this->whenLoaded('endereco')->first()),
        ];
    }
}
