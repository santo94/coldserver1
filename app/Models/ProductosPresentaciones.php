<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductosPresentaciones extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'ProductosPresentaciones';
    
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
     * Relación muchos a uno con UnidadesMedidas.
     * Una presentación de producto pertenece a una unidad de medida.
     */
    public function unidadMedida()
    {
        return $this->belongsTo(UnidadesMedidas::class, 'UnidadesMedidas', 'OID');
    }
    
    /**
     * Relación muchos a muchos con Empresas a través de EmpresaPPresentacion.
     * Una presentación de producto puede estar asociada a muchas empresas.
     */
    public function empresas()
    {
        return $this->belongsToMany(
            Empresa::class,
            'EmpresaPPresentacion',
            'ID_Empresa_PPrsentacion',
            'ID_Empresa_ProveedorE',
            'OID',
            'OID'
        );
    }
    
    /**
     * Relación directa con EmpresaPresentacion para acceso a la tabla pivote.
     */
    public function empresaPresentaciones()
    {
        return $this->hasMany(EmpresaPresentacion::class, 'ID_Empresa_PPrsentacion', 'OID');
    }
    
    /**
     * Relación muchos a uno con ABC.
     * Una presentación de producto pertenece a un código ABC.
     */
    public function codigoABC()
    {
        return $this->belongsTo(ABC::class, 'abc', 'OID');
    }
}
