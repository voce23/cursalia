<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateBuilder extends Model
{
    protected $fillable = [
        'title',
        'sub_title',
        'description',
        'background',
        'signature',
    ];
}
