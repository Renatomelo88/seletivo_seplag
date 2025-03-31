<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Unidade extends ModelBase
{
    protected $table = 'unidade';
    protected $fillable = [
        'nome',
        'sigla',
    ];

    public function endereco(): BelongsToMany
    {
        return $this->belongsToMany(Endereco::class, 'unidade_endereco', 'unidade_id', 'endereco_id');
    }

}
