<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "slug",
        "description",
        "restaurant_id"
    ];

    function getRouteKeyName()
    {
        return "slug";
    }

    function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    function plates(){
        return $this->belongsToMany(Plate::class, 'plate_menu');
    }
}
