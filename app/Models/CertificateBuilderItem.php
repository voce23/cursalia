<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateBuilderItem extends Model
{
    protected $fillable = [
        'element_id',
        'x_position',
        'y_position',
    ];
}