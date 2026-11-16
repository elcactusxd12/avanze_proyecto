<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Idioma extends Model
{
    protected $table = 'idiomas';

    protected $fillable = [
        'nombre',
        'codigo',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'idiomas_id');
    }
}