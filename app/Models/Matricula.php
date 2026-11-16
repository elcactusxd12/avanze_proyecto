<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matricula extends Model
{
    protected $table = 'matriculas';

    protected $fillable = [
        'fecha_matricula',
        'precio_total',
        'estado',
        'alumnos_id',
        'grupos_id',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
        'precio_total' => 'decimal:2',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'alumnos_id');
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupos_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'matriculas_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'matriculas_id');
    }
}