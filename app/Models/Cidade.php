<?php

namespace App\Models;

class Cidade extends ModelBase
{
    protected $table = 'cidade';

    protected $fillable = [
        'nome',
        'uf',
    ];
}
