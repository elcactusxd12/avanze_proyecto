<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Alumno extends Model
{
    protected $table = 'alumnos';

    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'direccion',
    ];

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'alumnos_id');
    }

    public function asistencias(): HasManyThrough
    {
        return $this->hasManyThrough(
            Asistencia::class,
            Matricula::class,
            'alumnos_id',
            'matriculas_id',
            'id',
            'id'
        );
    }

    public function pagos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Pago::class,
            Matricula::class,
            'alumnos_id',
            'matriculas_id',
            'id',
            'id'
        );
    }
}