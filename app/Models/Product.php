<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($product) {
            $product->offers()->detach();
        });
    }

    /**
     * Accessor: Convert the comma-separated string into an array when retrieving.
     */
    public function getIngredientsAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * Mutator: Convert the array into a comma-separated string when storing.
     */
    public function setIngredientsAttribute($value)
    {
        $this->attributes['ingredients'] = $value ? implode(',', $value) : null;
    }

    public function category() {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_product');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'product_order');
    }
}
