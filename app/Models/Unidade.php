<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function lotacao(): HasMany
    {
        return $this->hasMany(Lotacao::class, 'unidade_id', 'id');
    }

}
