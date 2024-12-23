<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'name', 'description', 'price', 'image', "slug"
    ];

    function getRouteKeyName()
    {
        return "slug";
    }

    function restaurant(){
        return $this->belongsTo(Restaurant::class);
    }

    function menus(){
        return $this->belongsToMany(Menu::class, 'plate_menu');
    }
}
