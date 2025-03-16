<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    //

    protected $guarded = [];

    /**
     * Summary of order
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Order, Rental>
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
    /**
     * Summary of item
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Item, Rental>
     */

    public function item()
    {
        return $this->hasOne(Item::class, 'model', 'model')->whereColumn('network', 'network');
    }
}
