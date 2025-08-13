<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadesMedidas extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'UnidadesMedidas';
    
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
     * Relación uno a muchos con ProductosPresentaciones.
     * Una unidad de medida tiene muchas presentaciones de productos.
     */
    public function productosPresentaciones()
    {
        return $this->hasMany(ProductosPresentaciones::class, 'UnidadesMedidas', 'OID');
    }
}
