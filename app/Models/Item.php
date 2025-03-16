<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
    protected $guarded = [];

    /**
     * Get the rentals associated with this item.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'model', 'model')
            ->whereColumn('rentals.network', 'items.network');
    }
}
