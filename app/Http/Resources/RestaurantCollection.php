<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RestaurantCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "restaurants" => $this->collection,
            "total" => $this->total(),
            "count" => $this->count(),
            "per_page" => $this->perPage(),
            "current_page" => $this->currentPage(),
            "last_page" => $this->lastPage(),
        ];
    }
}
