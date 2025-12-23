<?php

namespace App\Models;

use App\Models\BaseModel;

class PropertyType extends BaseModel
{
    protected $fillable = [
        'sort_order',
        'name',
        'slug',

        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function properties()
    {
        return $this->hasMany(Property::class, 'property_id', 'id');
    }
}
