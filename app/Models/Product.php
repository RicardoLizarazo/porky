<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category_id',
        'price',
        'description',
        'image',
        'packaging_cost',
        'allow_manual_price',
        'is_visible',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'packaging_cost' => 'float',
        'allow_manual_price' => 'boolean',
        'is_visible' => 'boolean',
        'is_active' => 'boolean',
    ];

    // 🔥 Agregar appends para que los accessors estén disponibles
    protected $appends = ['image_url', 'product_info'];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class)
            ->withPivot('is_available')
            ->withTimestamps();
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function rules()
    {
        return $this->belongsToMany(ProductRule::class,
            'product_rule_product'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /*
    |--------------------------------------------------------------------------
    | LÓGICA DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    public function finalPrice(): float
    {
        return $this->price + ($this->packaging_cost ?? 0);
    }

    public function isAvailableInLocation($locationId): bool
    {
        return $this->locations()
            ->where('location_id', $locationId)
            ->wherePivot('is_available', true)
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return asset('images/no-image.png');
        }
        
        return asset('storage/' . $this->image);
    }

    public function getProductInfoAttribute()
    {
        $codeHtml = !empty($this->code) 
            ? '<div class="text-muted small">' . e($this->code) . '</div>' 
            : '<div class="text-muted small">Sin código</div>';
        
        return '<div class="d-flex align-items-center">
                    <img src="' . $this->image_url . '" 
                         class="rounded mr-3" 
                         style="width:50px;height:50px;object-fit:cover;"
                         onerror="this.src=\'' . asset('images/no-image.png') . '\'">
                    <div>
                        <div class="font-weight-bold">' . e($this->name) . '</div>
                        ' . $codeHtml . '
                    </div>
                </div>';
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */
    
    public function newQuery()
    {
        $query = parent::newQuery();
        $query->select([
            'products.id',
            'products.name',
            'products.code',
            'products.category_id',
            'products.price',
            'products.description',
            'products.image',
            'products.packaging_cost',
            'products.allow_manual_price',
            'products.is_visible',
            'products.is_active',
            'products.created_at',
            'products.updated_at'
        ]);
        return $query;
    }
}