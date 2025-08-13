<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ABC extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'ABC';
    
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
     * Un código ABC tiene muchos productos presentaciones.
     */
    public function productosPresentaciones()
    {
        return $this->hasMany(ProductosPresentaciones::class, 'abc', 'OID');
    }
}
