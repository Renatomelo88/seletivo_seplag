<?php

namespace App\Models;

class Unidade extends ModelBase
{
    protected $table = 'unidade';
    protected $fillable = [
        'nome',
        'sigla',
    ];

}
