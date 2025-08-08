<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'total_amount',
        'payment_method',
        'status',
        'notes'
    ];
    
    /**
     * Get the client who made this purchase
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    
    /**
     * Get the items in this purchase
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
    
    /**
     * Recalculate the total amount based on items
     */
    public function recalculateTotal(): bool
    {
        $this->total_amount = $this->items()->sum('subtotal');
        return $this->save();
    }
    
    /**
     * Create a new purchase with items and update stock
     *
     * @param array $purchaseData Purchase data
     * @param array $items Items data (product_id, quantity)
     * @return Purchase|null
     */
    public static function createWithItems(array $purchaseData, array $items): ?Purchase
    {
        // Start a transaction
        \DB::beginTransaction();
        
        try {
            // Create the purchase
            $purchase = self::create($purchaseData);
            
            $totalAmount = 0;
            
            // Create the purchase items
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check if enough stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for product: {$product->name}");
                }
                
                // Create purchase item
                $purchaseItem = new PurchaseItem([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $product->price * $item['quantity']
                ]);
                
                $purchase->items()->save($purchaseItem);
                
                // Update product stock
                $product->decreaseStock($item['quantity']);
                
                $totalAmount += $purchaseItem->subtotal;
            }
            
            // Update purchase total
            $purchase->total_amount = $totalAmount;
            $purchase->save();
            
            \DB::commit();
            return $purchase;
            
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }
}
