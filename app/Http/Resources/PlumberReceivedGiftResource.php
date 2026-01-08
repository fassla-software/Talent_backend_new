<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlumberReceivedGiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'amount' => null,
            'payment_identifier' => null,
            'status'=>null,
            'transaction_type' => null,
            'request_date' => $this->createdAt,
            'processed_date' => $this->updatedAt,
            'is_gift'=>true,
        	'gift_name'=> $this->plumber_gift->name,
        	'points_required'=> $this->plumber_gift->points_required,
            'image' => $this->plumber_gift->image? 'https://app.talentindustrial.com/plumber/uploads/' . $this->plumber_gift->image: null,
       
        ];
    }
}
