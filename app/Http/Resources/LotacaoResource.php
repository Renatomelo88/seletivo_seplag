<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class LotacaoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'data_lotacao' => $this->data_lotacao,
            'data_remocao' => $this->data_remocao,
            'portaria' => $this->portaria,
            'unidade' => new UnidadeResource($this->whenLoaded('unidade')),
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
        ];
    }
}
