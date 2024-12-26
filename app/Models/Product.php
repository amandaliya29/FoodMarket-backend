<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
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
}
