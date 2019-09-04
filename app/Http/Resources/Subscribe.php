<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class Subscribe extends JsonResource
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
            'user_id' => $this->user_id,
            'plan_id' => $this->plan_id,
            'interval' => $this->interval,
            'quantity_bot' => $this->quantity_bot,
            'trial_ends_at' => $this->trial_ends_at,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'active' => $this->active,
            'last_invoice' => $this->last_invoice,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateTimeString() : null,
            'deleted_at' => $this->deleted_at ? Carbon::parse($this->deleted_at)->toDateTimeString() : null,
            'additional' => $this->additionals,
            'plan' => $this->plans,
        ];
    }
}
