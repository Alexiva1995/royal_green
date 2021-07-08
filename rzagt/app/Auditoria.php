<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    //
    protected $table = 'auditoria';

    protected $fillable = [
        'iduser', 'campo', 'valor_old', 'valor_new', 'code',
        'user_change', 'id_user_change', 'code_used'
    ];
}
