<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlumberWithdrawResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->amount,
            'payment_identifier' => $this->payment_identifier,
            'status'=>$this->status,
            'transaction_type' => $this->transaction_type,
            'request_date' => $this->request_date,
            'processed_date' => $this->processed_date,
            'is_gift'=>false,
        	'gift_name'=>null,
        	'points_required'=>null,
            'image' => null,
        ];
    }
}
