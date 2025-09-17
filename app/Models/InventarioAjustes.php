<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioAjustes extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'InventariosAjustes';
    
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
     * Relación uno a muchos con ProductosPresentaciones.
     * Un código ABC tiene muchos productos presentaciones.
     */
    public function contenedorajust()
    {
        return $this->hasOne(Contenedores::class, 'OID', 'Contenedor');
    }

    public function Prod_pre()
    {
        return $this->hasOne(ProductosPresentaciones::class, 'OID','ProductosPresentaciones');
    }

    public function ubic()
    {
        return $this->hasOne(Ubicaciones::class, 'OID', 'Ubicaciones');
    }
    public function usuarioajuste()
    {
        return $this->hasOne(Usuario::class,'Oid','Usuarios');
    }
}