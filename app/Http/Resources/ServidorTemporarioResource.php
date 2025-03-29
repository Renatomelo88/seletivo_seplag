<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServidorTemporarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pessoa_id' => $this->pessoa_id,
            'data_admissao' => $this->data_admissao,
            'data_demissao' => $this->data_demissao,
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
        ];
    }
}
