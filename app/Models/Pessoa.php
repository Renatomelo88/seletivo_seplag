<?php

namespace App\Models;

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

}
