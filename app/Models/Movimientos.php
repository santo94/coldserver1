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
     * Relación muchos a uno con MovimientosTipos.
     * Un movimiento pertenece a un tipo de movimiento.
     */
    public function movimientoTipo()
    {
        return $this->belongsTo(MovimientosTipos::class, 'MovimientosTipos', 'OID');
    }
    
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
    
    /**
     * Relación muchos a uno con Ubicaciones (Origen).
     * Un movimiento tiene una ubicación de origen.
     */
    public function ubicacionOrigen()
    {
        return $this->belongsTo(Ubicaciones::class, 'UbicacionesOrigenes', 'OID');
    }
    
    /**
     * Relación muchos a uno con Ubicaciones (Destino).
     * Un movimiento tiene una ubicación de destino.
     */
    public function ubicacionDestino()
    {
        return $this->belongsTo(Ubicaciones::class, 'UbicacionesDestinos', 'OID');
    }
    
    /**
     * Relación muchos a uno con Contenedores.
     * Un movimiento pertenece a un contenedor.
     */
    public function contenedor()
    {
        return $this->belongsTo(Contenedores::class, 'Contenedores', 'OID');
    }

   
}
