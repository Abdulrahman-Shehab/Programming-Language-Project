<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // 'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', fn() => $this->user->first_name . ' ' . $this->user->last_name),
            'title' => $this->title,
            'description' => $this->description,
            'area' => $this->area,
            'governorate_name' => $this->whenLoaded('governorate', fn() => $this->governorate->name),
            'city_name' => $this->whenLoaded('city', fn() => $this->city->name),
            'address' => $this->address,
            'daily_price' => $this->daily_price,
            'image1' => $this->image1,
            'image2' => $this->image2,
            'image3' => $this->image3,
            'image4' => $this->image4,
            'image5' => $this->image5,
        ];
    }
}
