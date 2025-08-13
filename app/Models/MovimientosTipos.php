<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientosTipos extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'MovimientosTipos';
    
    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'OID';
    
    /**
     * Indica si el modelo debe manejar timestamps.
     */
    public $timestamps = false;
    
    /**
     * Los atributos que se pueden asignar masivamente (solo lectura).
     */
    protected $fillable = [];
    
    /**
     * Los atributos que deben ser protegidos de asignación masiva.
     */
    protected $guarded = ['*'];
    
    /**
     * Relación uno a muchos con Movimientos.
     * Un tipo de movimiento tiene muchos movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(Movimientos::class, 'MovimientosTipos', 'OID');
    }
}
