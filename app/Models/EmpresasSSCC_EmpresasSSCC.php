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
    protected $primaryKey = 'SSCC';
    
    /**
     * Indica si el modelo debe manejar timestamps.
     */
    public $timestamps = false;
    
    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'SSCC',
        'oidEmpresa',
    ];
    
    /**
     * Los atributos que deben ser protegidos de asignación masiva.
     */
    protected $guarded = ['*'];
    
    /**
     * Relación muchos a uno con Empresa.
     * Un SSCC pertenece a una empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'oidEmpresa', 'OID');
    }
    
    /**
     * Relación uno a uno con Contenedores.
     * Un SSCC tiene un contenedor asociado.
     */
    public function contenedor()
    {
        return $this->belongsTo(Contenedores::class, 'SSCC', 'SSCC');
    }
}