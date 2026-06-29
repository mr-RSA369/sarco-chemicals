<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemBundleSize extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'bundle_size',
        'available_bundles'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function transactions()
    {
        return $this->hasMany(
            InventoryTransaction::class
        );
    }

}
