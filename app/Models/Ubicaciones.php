<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicaciones extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Ubicaciones';
    
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
     * Relación muchos a uno con UbicacionesTipos.
     * Una ubicación pertenece a un tipo de ubicación.
     */
    public function ubicacionTipo()
    {
        return $this->belongsTo(UbicacionesTipos::class, 'UbicacionesTipos', 'OID');
    }
}
