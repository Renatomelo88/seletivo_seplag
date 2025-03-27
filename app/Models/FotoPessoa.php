<?php

namespace App\Models;

class FotoPessoa extends ModelBase
{
    protected $table = 'foto_pessoa';
    protected $fillable = [
        'pessoa_id',
        'data',
        'bucket',
        'hash',
    ];

}
