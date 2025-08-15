<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmpresaTipo;
use App\Models\Contacto;
use App\Models\ProductosPresentacion;
use App\Models\EmpresasPresentacion;

class Datoscontenedor extends Model
{
    use HasFactory;

    protected $table = 'datoscontenedor';
    protected $primaryKey = 'Id_datos';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'ContenedorOid',
        'altura',
        'tipo',
        'fecha',
    ];

    public $timestamps = false;

   
    public function empresaTipo()
    {
        return $this->belongsTo(EmpresaTipo::class, 'EmpresasTipos', 'OID');
    }


    public function contactos()
    {
        return $this->hasMany(Contacto::class);
    }

    // Relación muchos a muchos con ProductosPresentacion a través de EmpresasPresentacion
    public function productosPresentaciones()
    {
        return $this->belongsToMany(
            ProductosPresentacion::class,
            'empresasppresentacion', // tabla intermedia
            'ID_Empresa_ProveedorE', // FK en tabla intermedia que apunta a empresas
            'ID_Empresa_PPrsentacion', // FK en tabla intermedia que apunta a productos_presentaciones
            'OID', // PK de empresas
            'OID'  // PK de productos_presentaciones
        )->using(EmpresasPresentacion::class);
    }

    // Relación a través de la tabla intermedia para acceso directo
    public function empresasPresentaciones()
    {
        return $this->hasMany(EmpresasPresentacion::class, 'ID_Empresa_ProveedorE', 'OID');
    }   
}