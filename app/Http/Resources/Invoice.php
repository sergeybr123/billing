<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Invoice extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'manager_id' => $this->manager_id,
            'user_id' => $this->user_id,
            'amount' => $this->amount,
            'type' => $this->types,
            'plan' => $this->plan,
            'service' => $this->service,
            'description' => $this->description,
            'paid' => $this->paid,
            'paid_at' => $this->paid_at ? Carbon::parse($this->paid_at)->toDateTimeString() : null,
            'options' => $this->options,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateTimeString() : null,
            'deleted_at' => $this->deleted_at ? Carbon::parse($this->deleted_at)->toDateTimeString() : null,
        ];
    }
}
