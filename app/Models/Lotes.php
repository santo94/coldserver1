<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lotes extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Lotes';
    
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
        'OID',
        'Lote',
        'FechaCaducidad',
        'FechaRecepcion',
        'OptimisticLockField',
        'GCRecord',
    ];
    
    /**
     * Los atributos que deben ser protegidos de asignación masiva.
     */
    protected $guarded = ['*'];
    
    /**
     * Relación uno a muchos con Contenedores.
     * Un lote tiene muchos contenedores.
     */
    public function contenedores()
    {
        return $this->hasMany(Contenedores::class, 'LotesContenedores', 'OID');
    }
    
    /**
     * Relación uno a muchos con Movimientos.
     * Un lote tiene muchos movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(Movimientos::class, 'Lotes', 'OID');
    }
}
