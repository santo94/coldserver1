<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpresasTipos extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'EmpresasTipos';
    
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
     * Relación uno a muchos con Empresas.
     * Un tipo de empresa puede tener muchas empresas.
     */
    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'EmpresasTipos', 'OID');
    }
}
