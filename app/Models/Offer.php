<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($offer) {
            $offer->products()->detach();
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product');
    }
}
