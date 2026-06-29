<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'inventory_batch_id',
        'transaction_date',
        'transaction_type',
        'quantity',
        // 'qty_per_bundle',
        'item_bundle_size_id',
        'bundle_count',
        'remarks',
        'opening_balance',
        'closing_balance',
        'created_by'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(
            InventoryBatch::class
        );
    }

    public function bundleSize()
    {
        return $this->belongsTo(
            ItemBundleSize::class,
            'item_bundle_size_id'
        );
    }
}
