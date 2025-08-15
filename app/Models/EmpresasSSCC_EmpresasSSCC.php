<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresasSSCC_EmpresasSSCC extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'EmpresasSSCC_EmpresasSSCC';
    
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
     * Relación muchos a uno con EmpresasTipos.
     * Una empresa pertenece a un tipo de empresa.
     */
    public function empresasc()
    {
        return $this->belongsTo(Empresa::class, 'oidEmpresa', 'OID');
    }
    
    /**
     * Relación muchos a muchos con ProductosPresentaciones a través de EmpresaPPresentacion.
     * Una empresa puede tener muchas presentaciones de productos.
     */
   
    
    /**
     * Relación directa con EmpresaPresentacion para acceso a la tabla pivote.
     */
    public function empresaPresentaciones()
    {
        return $this->hasMany(EmpresaPresentacion::class, 'ID_Empresa_ProveedorE', 'OID');
    }
}