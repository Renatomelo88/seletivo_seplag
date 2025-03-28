<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServidorEfetivo extends ModelBase
{
    protected $table = 'servidor_efetivo';
    protected $primaryKey = 'pessoa_id';

    protected $fillable = [
        'pessoa_id',
        'matricula',
    ];

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

}
