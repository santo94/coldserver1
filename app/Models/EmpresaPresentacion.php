<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresaPresentacion extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'EmpresaPPresentacion';
    
    /**
     * La clave primaria de la tabla.
     */
    protected $primaryKey = 'OID';
    
    /**
     * Indica que la clave primaria no es auto-incremental (UUID).
     */
    public $incrementing = false;
    
    /**
     * El tipo de datos de la clave primaria.
     */
    protected $keyType = 'string';
    
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
     * Relación muchos a uno con Empresas.
     * Una relación empresa-presentación pertenece a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'ID_Empresa_ProveedorE', 'OID');
    }
    
    /**
     * Relación muchos a uno con ProductosPresentaciones.
     * Una relación empresa-presentación pertenece a una presentación de producto.
     */
    public function productoPresentacion()
    {
        return $this->belongsTo(ProductosPresentaciones::class, 'ID_Empresa_PPrsentacion', 'OID');
    }
}
