<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesServicios extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'OrdenesServicios';
    
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
     * Relación muchos a uno con Ordenes.
     * Una orden-servicio pertenece a una orden.
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'OIDOrden', 'OID');
    }
    
    /**
     * Relación muchos a uno con Servicios.
     * Una orden-servicio pertenece a un servicio.
     */
    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'OIDServicio', 'OID');
    }
}
