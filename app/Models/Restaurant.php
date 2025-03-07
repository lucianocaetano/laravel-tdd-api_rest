<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        "name",
        "description",
        "slug",
        "user_id"
    ];

    function getRouteKeyName()
    {
        return "slug";
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }

    function plates()
    {
        return $this->hasMany(Plate::class);
    }

    function menus()
    {
        return $this->hasMany(Menu::class);
    }

    protected $filter_fields = [
        "name",
        "description",
    ];

    protected $sort_fields = [
        "name",
        "description",
        "created_at",
        "updated_at",
        "id"
    ];
}
