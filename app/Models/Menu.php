<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, Filterable;

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

    function plates()
    {
        return $this->belongsToMany(Plate::class, 'plate_menu');
    }

    protected $filter_fields = [
        "name",
        "description",
    ];

    protected $sort_fields = [
        "name",
        "description",
        "created_at",
        "updated_at"
    ];
}
