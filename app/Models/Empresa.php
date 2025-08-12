<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'Empresas';
    
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
    public function empresaTipo()
    {
        return $this->belongsTo(EmpresasTipos::class, 'EmpresasTipos', 'OID');
    }
}
