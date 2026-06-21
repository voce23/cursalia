<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ajustes clave-valor de los complementos PRO (p. ej. la llave PRO).
 */
class ProSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];
}
