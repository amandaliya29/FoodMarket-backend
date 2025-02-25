<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_order');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'id', 'receipts_id');
    }
}
