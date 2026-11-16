<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'horario',
        'cupo_maximo',
        'cursos_id',
        'profesores_id',
    ];

    /**
     * Conversión automática de fechas.
     */
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    /**
     * Un grupo pertenece a un curso.
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'cursos_id', 'id');
    }

    /**
     * Un grupo pertenece a un profesor.
     */
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesores_id', 'id');
    }

    /**
     * Un grupo tiene muchas matrículas.
     */
    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'grupos_id', 'id');
    }
}