<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_category_id',
        'name',
        'description',
        'price',
        'stock',
        'alert_threshold',
        'image',
    ];
    
    /**
     * Get the category for this product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
    
    /**
     * Get the purchase items for this product
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    /**
     * Check if the product stock is low (below threshold)
     * 
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->stock < $this->alert_threshold;
    }
    
    /**
     * Decrease stock by given quantity
     * 
     * @param int $quantity
     * @return bool
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            return $this->save();
        }
        
        return false;
    }
    
    /**
     * Increase stock by given quantity
     * 
     * @param int $quantity
     * @return bool
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        return $this->save();
    }
}
