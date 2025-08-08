<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseItem extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal'
    ];
    
    /**
     * Get the purchase this item belongs to
     */
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    
    /**
     * Get the product for this purchase item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Calculate the subtotal before saving
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($purchaseItem) {
            if (!isset($purchaseItem->subtotal)) {
                $purchaseItem->subtotal = $purchaseItem->unit_price * $purchaseItem->quantity;
            }
        });
    }
}
