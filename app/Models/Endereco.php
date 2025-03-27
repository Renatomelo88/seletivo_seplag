<?php

namespace App\Models;

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

}
