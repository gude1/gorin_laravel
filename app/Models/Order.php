<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //

    protected $guarded = [];

    /**
     * Summary of rentals
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Rental, Order>
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'order_id', 'id');
    }
}
