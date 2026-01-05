<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->whenLoaded('user', fn() => $this->user->first_name . ' ' . $this->user->last_name),
            'user_id' => $this->user_id,
            'governorate_name' => $this->whenLoaded('governorate', fn() => $this->governorate->name),
            'governorate_id' => $this->governorate_id,
            'city_name' => $this->whenLoaded('city', fn() => $this->city->name),
            'city_id' => $this->city_id,
            'title' => $this->title,
            'area' => $this->area,
            'description' => $this->description,
            'daily_price' => $this->daily_price,
            'address' => $this->address,
            'created_at' => $this->created_at,
        ];
    }
}
