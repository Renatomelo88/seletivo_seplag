<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ServidorUnidadeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'pessoa' => new PessoaResource($this->whenLoaded('pessoa')),
            'unidade' => new UnidadeResource($this->pessoa->lotacao->unidade),
        ];
    }
}
