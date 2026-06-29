<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryBatch extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'item_id',
        'batch_no',
        'mfg_date',
        'exp_date',
        'rack_no',
        'qty_per_bundle',
        'current_qty',
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
