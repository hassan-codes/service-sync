<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'  =>  $this->id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'type' => $this->type,
            'posted_by' => $this->user,
            'posted_at' => $this->posted_at,
            'reference' => $this->reference,
            'status' => $this->status
        ];
    }
}
