<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'product_slug',
        'image',
        'link',
        'position',
        'status',
    ];
}
