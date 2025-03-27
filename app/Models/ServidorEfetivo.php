<?php

namespace App\Models;

class ServidorEfetivo extends ModelBase
{
    protected $table = 'servidor_efetivo';
    protected $primaryKey = 'pessoa_id';

    protected $fillable = [
        'pessoa_id',
        'matricula',
    ];

}
