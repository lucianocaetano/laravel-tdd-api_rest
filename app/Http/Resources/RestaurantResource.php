<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'user' => $this->user->name,
            'links' => [
                'self' => route('restaurant.show', ['restaurant' => $this->slug]),
                'index' => route('restaurant.index'),
                'store' => route('restaurant.store'),
                'update' => route('restaurant.update', ['restaurant' => $this->slug]),
                'delete' => route('restaurant.destroy', ['restaurant' => $this->slug]),
            ]
        ];
    }
}
