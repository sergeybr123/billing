<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdditionalSubscribeCollection;
use Illuminate\Http\Request;
use App\AdditionalSubscribe;
use App\Http\Resources\AdditionalSubscribe as AdditionalResource;

class AdditionalSubscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return AdditionalResource::collection(AdditionalSubscribe::all());
    }

    public function show($id)
    {
        $additional = AdditionalSubscribe::where('subscribe_id', $id)->get();
        return AdditionalResource::collection($additional);
    }
}
