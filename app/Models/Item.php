<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'category_id',
        'type_id',
        'product_name',
        'item_name',
        'size',
        'size_unit',
        'minimum_qty',
    ];

    public function batches()
    {
        return $this->hasMany(
            InventoryBatch::class
        );
    }

    public function transactions()
    {
        return $this->hasMany(
            InventoryTransaction::class
        );
    }

    public function category()
    {
        return $this->belongsTo(
            ItemCategory::class,
            'category_id'
        );
    }

    public function type()
    {
        return $this->belongsTo(
            ItemType::class,
            'type_id'
        );
    }

    public function bundleSizes()
    {
        return $this->hasMany(
            ItemBundleSize::class
        );
    }

}
