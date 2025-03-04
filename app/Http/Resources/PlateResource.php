<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'image' => $this->image,
            'restaurant' => $this->restaurant->name,
            'links' => [
                'self' => route('plate.show', ['restaurant' => $this->restaurant->slug, 'plate' => $this->id]),
                'index' => route('plate.index', ['restaurant' => $this->restaurant->slug]),
                'store' => route('plate.store', ['restaurant' => $this->restaurant->slug]),
                'update' => route('plate.update', ['restaurant' => $this->restaurant->slug, 'plate' => $this->id]),
                'delete' => route('plate.destroy', ['restaurant' => $this->restaurant->slug, 'plate' => $this->id]),
            ]
        ];
    }
}
