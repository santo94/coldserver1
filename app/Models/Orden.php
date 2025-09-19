<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Ordenes';
    
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
    protected $fillable = [
        'Estatus',
    ];
    
    /**
     * Los atributos que deben ser protegidos de asignación masiva.
     */
    protected $guarded = ['*'];
    
    /**
     * Relación muchos a uno con OrdenesTipos.
     * Una orden pertenece a un tipo de orden.
     */
    public function ordenTipo()
    {
        return $this->belongsTo(OrdenesTipos::class, 'OrdenesTipos', 'OID');
    }
    
    /**
     * Relación uno a muchos con OrdenesProductosPresentaciones.
     * Una orden puede tener muchos productos presentaciones.
     */
    public function productosPresententaciones()
    {
        return $this->hasMany(OrdenProductoPresentacion::class, 'Ordenes', 'OID');
    }

    public function usuar()
    {
        return $this->hasOne(Usuario::class, 'Oid', 'Usuario');
    }

    
    /**
     * Relación uno a uno con OrdenesDetalles.
     * Una orden tiene un detalle.
     */
    public function detalle()
    {
        return $this->hasOne(OrdenDetalle::class, 'Ordenes', 'OID');
    }
    
    /**
     * Relación muchos a muchos con Servicios a través de OrdenesServicios.
     * Una orden puede tener muchos servicios.
     */
    public function servicios()
    {
        return $this->belongsToMany(
            Servicios::class,
            'OrdenesServicios',
            'OIDOrden',
            'OIDServicio',
            'OID',
            'OID'
        );
    }
    // Relación correcta: Una orden pertenece a un cliente
    public function cliente()
    {
        return $this->belongsTo(Empresas::class, 'OidProveedor', 'OID');
    }

    // Relación correcta: Una orden pertenece a un proveedor  
    public function proveedor()
    {
        return $this->belongsTo(Empresas::class, 'OidCliente', 'OID');
    }

    /*
    public function cliente()
    {
        return $this->hasOne(Empresas::class,'OID','OidProveedor');
    }

    public function proveedor()
    {
        return $this->hasOne(Empresas::class,'OID','OidCliente');
    }*/
    
    /**
     * Relación directa con OrdenesServicios para acceso a la tabla pivote.
     */

    // Esta orden pertenece a otra orden (la de servicio)
    public function ordenDeServicio()
    {
        return $this->belongsTo(Orden::class, 'CodigoRastreo', 'Codigo');
    }

    public function ordenesServicios()
    {
        return $this->hasMany(OrdenesServicios::class, 'OIDOrden', 'OID');
    }
}
