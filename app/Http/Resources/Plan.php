<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PlanFeaturesCollection;
use App\Http\Resources\FeatureCollection;
use Carbon\Carbon;

class Plan extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'discount' => $this->discount,
            'description' => $this->description,
            'price' => $this->price,
            'interval' => $this->interval,
            'trial_period_days' => $this->trial_period_days,
            'sort_order' => $this->sort_order,
            'on_show' => $this->on_show,
            'active' => $this->active,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toDateTimeString() : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->toDateTimeString() : null,
            'features' => $this->features,
        ];
    }
}
