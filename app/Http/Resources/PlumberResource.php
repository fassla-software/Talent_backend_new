<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlumberResource extends JsonResource
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
        	'name' => $this->user->name,
            'phone' => $this->user->phone,
            'email' => $this->user->email,
            'is_active' => (bool) $this->user->is_active,
            'profile_photo' => $this->user->thumbnail,
            'gender' => $this->user->gender,
            'date_of_birth' => $this->user->date_of_birth,
        	'city' => $this->city,
        	'area' => $this->area,
        	'city' => $this->city,
        	'is_verified' => $this->is_verified,
        	'expiration_date' => $this->expiration_date,
        	'instant_withdrawal' => $this->instant_withdrawal,
        	'gift_points' => $this->gift_points,
        	'fixed_points' => $this->fixed_points,
        	'image' => $this->image? 'https://app.talentindustrial.com/plumber/uploads/' . $this->image: null,
        	'withdraw_money' => $this->withdraw_money,
        ];
    }
}
