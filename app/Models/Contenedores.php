<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenedores extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Contenedores';
    
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
     * Relación muchos a uno con Lotes.
     * Un contenedor pertenece a un lote.
     */
    public function lote()
    {
        return $this->belongsTo(Lotes::class, 'LotesContenedores', 'OID');
    }
    
    /**
     * Relación uno a muchos con Movimientos.
     * Un contenedor tiene muchos movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(Movimientos::class, 'Contenedores', 'OID');
    }

     public function ProdUbicExis(){
        return $this->hasOne(ProductosUbicacionesExistencia::class, 'ContenedorOid', 'OID');
    }
    public function datos(){
        return $this->hasOne(Datoscontenedor::class,'ContenedorOid', 'OID');
    }

    public function sscc_ep()
    {
        return $this->hasOne(EmpresasSSCC_EmpresasSSCC::class,'SSCC','SSCC');
    }
}
