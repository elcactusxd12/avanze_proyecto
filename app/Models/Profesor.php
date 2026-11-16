<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesor extends Model
{
    protected $table = 'profesores';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'especialidad',
    ];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'profesores_id');
    }
}