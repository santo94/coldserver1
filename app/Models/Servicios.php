<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Servicios';
    
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
     * Relación muchos a muchos con Ordenes a través de OrdenesServicios.
     * Un servicio puede estar en muchas órdenes.
     */
    public function ordenes()
    {
        return $this->belongsToMany(
            Orden::class,
            'OrdenesServicios',
            'OIDServicio',
            'OIDOrden',
            'OID',
            'OID'
        );
    }
    
    /**
     * Relación directa con OrdenesServicios para acceso a la tabla pivote.
     */
    public function ordenesServicios()
    {
        return $this->hasMany(OrdenesServicios::class, 'OIDServicio', 'OID');
    }
}
