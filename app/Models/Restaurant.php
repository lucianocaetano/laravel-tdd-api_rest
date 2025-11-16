<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *      required={"name", "description", "slug", "user_id"},
 *      @OA\Property(
 *          property="name",
 *          type="string",
 *          description="Name of the restaurant",
 *          default="My Restaurant",
 *      ),
 *      @OA\Property(
 *          property="description",
 *          type="string",
 *          description="Description of the restaurant",
 *          default="My description",
 *      ),
 *      @OA\Property(
 *          property="slug",
 *          type="string",
 *          description="Slug of the restaurant",
 *          default="my-restaurant",
 *      ),
 *      @OA\Property(
 *          property="user_id",
 *          type="integer",
 *          description="ID of the user",
 *          default=1,
 *      )
 *  )
 */

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
