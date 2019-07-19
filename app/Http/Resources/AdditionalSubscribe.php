<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AdditionalSubscribe extends JsonResource
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
            'subscribe_id' => $this->subscribe_id,
            'additional_type' => $this->type()->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'trial_ends_at' => $this->trial_ends_at ? Carbon::parse($this->trial_ends_at)->toDateTimeString() : null,
            'start_at' => $this->start_at ? Carbon::parse($this->start_at)->toDateString() : null,
            'end_at' => $this->end_at ? Carbon::parse($this->end_at)->toDateString() : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toDateString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateString() : null,
            'deleted_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateString() : null,
        ];
    }
}
