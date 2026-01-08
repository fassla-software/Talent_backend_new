<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlumberFinalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        private $withdraw;
    private $gift;

    public function __construct($withdraw, $gift)
    {
        $this->withdraw = $withdraw;
        $this->gift = $gift;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'amount' => $this->withdraw->amount ?? $this->gift->amount,
            'status' => $this->withdraw->status ?? $this->gift->status,
            'payment_identifier' => $this->withdraw->payment_identifier ?? null,
            'transaction_type' => $this->withdraw->transaction_type ?? null,
            'request_date' => $this->withdraw->request_date ?? null,
            'processed_date' => $this->withdraw->processed_date ?? null,
            'image' => $this->withdraw->image ?? null,
            'is_gift' => $this->gift !== null,
        ];
    }
    }
}
