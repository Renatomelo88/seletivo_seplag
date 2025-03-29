<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServidorTemporario extends ModelBase
{
    protected $table = 'servidor_temporario';
    protected $primaryKey = 'pessoa_id';
    protected $fillable = [
        'pessoa_id',
        'data_admissao',
        'data_demissao',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }
}
