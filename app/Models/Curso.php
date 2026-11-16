<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'cursos';

    protected $fillable = [
        'nombre',
        'nivel',
        'duracion_horas',
        'precio',
        'idiomas_id',
    ];

    protected $casts = [
        'duracion_horas' => 'integer',
        'precio' => 'decimal:2',
    ];

    public function idioma(): BelongsTo
    {
        return $this->belongsTo(Idioma::class, 'idiomas_id');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'cursos_id');
    }
}