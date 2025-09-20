<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProductoPresentacion extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'OrdenesProductosPresentaciones';
    
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
     * Relación muchos a uno con Ordenes.
     * Un producto presentación pertenece a una orden.
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'Ordenes', 'OID');
    }

    public function ProdPre()
    {
        return $this->belongsTo(ProductosPresentaciones::class,'productosPresentaciones','OID');
    }
    
    /**
     * Relación uno a muchos con Movimientos.
     * Una orden producto presentación tiene muchos movimientos.
     */
    public function movimientos()
    {
        return $this->hasMany(Movimientos::class, 'ordenesProductosPresentaciones', 'OID');
    }

    public function MovimientoEntrada()
    {
        return $this->hasOne(Movimientos::class, 'ordenesProductosPresentaciones','OID');
    }

    public function unidmed()
    {
        return $this->hasOne(UnidadesMedidas::class,'OID','UnidadesMedidas');
    }

    public function contenedor()
    {
        return $this->hasOne(Contenedores::class,'SSCC','SSCC');
    }
}
