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
    protected $fillable = [
        'OID',
        'Codigo',
        'Estatus',
        'Tipo',
        'Oid_Padre',
        'Consecutivo',
        'LotesContenedores',
        'FechaProduccion',
        'FechaRecepcion',
        'Batch',
        'SSCC',
        'LoteProveedor',
        'Descripcion',
        'OptimisticLockField',
        'GCRecord',
        'Altura',
        'Temperatura',
    ];
    
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

    public function movimientoEntrada()
    {
        return $this->hasOne(Movimientos::class,'Contenedores','OID')->where('MovimientosTipos', 1);
    }

     public function ProdUbicExis(){
        return $this->hasOne(ProductosUbicacionesExistencia::class, 'ContenedorOid', 'OID');
    }

    public function prodhist(){
        return $this->hasMany(ProductosUbicacionesExistenciaHistorico::class,'ContenedorOid', 'OID');
    }

    public function ultimaExistenciaHistorico()
        {
            return $this->hasOne(ProductosUbicacionesExistenciaHistorico::class, 'ContenedorOid', 'OID')
                        ->latest('FechaInsercionDatosHistorico'); // suponiendo que tu campo de fecha se llama "Fecha"
        }
    public function datos(){
        return $this->hasOne(Datoscontenedor::class,'ContenedorOid', 'OID');
    }

    public function sscc_ep()
    {
        return $this->hasOne(EmpresasSSCC_EmpresasSSCC::class,'SSCC','SSCC');
    }

    public function Ajustes()
    {
        return $this->hasMany(InventarioAjustes::class, 'Contenedor','OID');
    }

    public function ordprodpre(){
        return $this->hasOne(OrdenProductoPresentacion::class,'SSCC','SSCC');
    }
}
