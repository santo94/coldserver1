<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Movimientos';
    
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
     * Relación muchos a uno con Lotes.
     * Un movimiento pertenece a un lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lotes::class, 'Lotes', 'OID');
    }
    
    /**
     * Relación muchos a uno con OrdenProductoPresentacion.
     * Un movimiento pertenece a una orden producto presentación.
     */
    public function ordenProductoPresentacion()
    {
        return $this->belongsTo(OrdenProductoPresentacion::class, 'ordenesProductosPresentaciones', 'OID');
    }
}
