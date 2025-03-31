<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pessoa extends ModelBase
{
    protected $table = 'pessoa';
    protected $fillable = [
        'nome',
        'data_nascimento',
        'sexo',
        'mae',
        'pai',
    ];

    protected $dates = ['data_nascimento'];

    public function foto(): HasOne
    {
        return $this->hasOne(FotoPessoa::class, 'pessoa_id', 'id');
    }

    public function endereco(): BelongsToMany
    {
        return $this->belongsToMany(Endereco::class, 'pessoa_endereco', 'pessoa_id', 'endereco_id');
    }

    public function lotacao(): HasOne
    {
        return $this->hasOne(Lotacao::class, 'pessoa_id', 'id');
    }

}
