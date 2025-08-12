<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProductoPresentacion extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'OrdenesProductosPresentaciones';
    
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
     * Un producto presentación pertenece a una orden.
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'Ordenes', 'OID');
    }
}
