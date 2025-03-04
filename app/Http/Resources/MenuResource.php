<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
            "slug" => $this->slug,
            "description" => $this->description,
            "restaurant" => $this->restaurant->name,
            'links' => [
                'self' => route('menu.show', ['restaurant' => $this->restaurant->slug, 'menu' => $this->id]),
                'index' => route('menu.index', ['restaurant' => $this->restaurant->slug]),
                'store' => route('menu.store', ['restaurant' => $this->restaurant->slug]),
                'update' => route('menu.update', ['restaurant' => $this->restaurant->slug, 'menu' => $this->id]),
                'delete' => route('menu.destroy', ['restaurant' => $this->restaurant->slug, 'menu' => $this->id]),
            ],
        ];
    }
}
