<?php

namespace App\Models;

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

}
