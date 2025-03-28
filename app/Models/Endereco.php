<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Endereco extends ModelBase
{
    protected $table = 'endereco';
    protected $fillable = [
        'tipo_logradouro',
        'logradouro',
        'numero',
        'bairro',
        'cidade_id',
    ];

    public function cidade(): BelongsTo{
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

}
