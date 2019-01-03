<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AdditionalSubscribe;
use App\Http\Resources\AdditionalSubscribe as AdditionalResource;

class AdditionalSubscribeController extends Controller
{
    public function show($id)
    {
        $additional = AdditionalSubscribe::where('subscribe_id', $id)->get();
        return AdditionalResource::collection($additional);
    }
}
