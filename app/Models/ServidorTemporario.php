<?php

namespace App\Models;

class ServidorTemporario extends ModelBase
{
    protected $table = 'servidor_temporario';
    protected $primaryKey = 'pessoa_id';
    protected $fillable = [
        'pessoa_id',
        'data_admissao',
        'data_demissao',
    ];


}
