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
    protected $fillable = [];
    
    /**
     * Los atributos que deben ser protegidos de asignación masiva.
     */
    protected $guarded = ['*'];
    
    /**
     * Relación uno a muchos con OrdenesProductosPresentaciones.
     * Una orden puede tener muchos productos presentaciones.
     */
    public function productosPresententaciones()
    {
        return $this->hasMany(OrdenProductoPresentacion::class, 'Ordenes', 'OID');
    }
}
