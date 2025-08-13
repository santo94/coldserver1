<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesTipos extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'OrdenesTipos';
    
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
     * Relación uno a muchos con Ordenes.
     * Un tipo de orden tiene muchas órdenes.
     */
    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'OrdenesTipos', 'OID');
    }
}
