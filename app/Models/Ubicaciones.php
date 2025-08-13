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
    
    /**
     * Relación recursiva: Ubicación padre.
     * Una ubicación puede tener una ubicación padre.
     */
    public function ubicacionPadre()
    {
        return $this->belongsTo(Ubicaciones::class, 'UbicacionP', 'OID');
    }
    
    /**
     * Relación recursiva: Ubicaciones hijas.
     * Una ubicación puede tener muchas ubicaciones hijas.
     */
    public function ubicacionesHijas()
    {
        return $this->hasMany(Ubicaciones::class, 'UbicacionP', 'OID');
    }
    
    /**
     * Relación recursiva: Todas las ubicaciones descendientes.
     * Obtiene todas las ubicaciones hijas de forma recursiva.
     */
    public function ubicacionesDescendientes()
    {
        return $this->ubicacionesHijas()->with('ubicacionesDescendientes');
    }
    
    /**
     * Relación uno a muchos con Movimientos (como origen).
     * Una ubicación puede ser origen de muchos movimientos.
     */
    public function movimientosOrigen()
    {
        return $this->hasMany(Movimientos::class, 'UbicacionesOrigenes', 'OID');
    }
    
    /**
     * Relación uno a muchos con Movimientos (como destino).
     * Una ubicación puede ser destino de muchos movimientos.
     */
    public function movimientosDestino()
    {
        return $this->hasMany(Movimientos::class, 'UbicacionesDestinos', 'OID');
    }
    
    /**
     * Obtener todos los movimientos relacionados con esta ubicación (origen y destino).
     */
    public function todosLosMovimientos()
    {
        // Esta es una función de conveniencia que puedes usar en consultas
        return Movimientos::where('UbicacionesOrigenes', $this->OID)
                         ->orWhere('UbicacionesDestinos', $this->OID);
    }
}
