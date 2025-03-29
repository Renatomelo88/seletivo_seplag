<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lotacao extends ModelBase
{
    protected $table = 'lotacao';
    protected $fillable = [
        'pessoa_id',
        'unidade_id',
        'data_lotacao',
        'data_remocao',
        'portaria',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    public function unidade(): BelongsTo
    {
        return $this->belongsTo(Unidade::class, 'unidade_id');
    }

}
