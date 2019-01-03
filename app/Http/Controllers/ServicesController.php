<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Service as ServiceResource;
use App\Service;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ServiceResource::collection(Service::where('active', true)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service = Service::create($request->all());
        if($service) {
            return ['error' => 0, 'message' => 'Запись успешно добавлена'];
        } else {
            return ['error' => 1, 'message' => 'Ошибка добавления записи'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ServiceResource(Service::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        if($service) {
            $service->update($request->all());
        } else {
            return ['error' => 1, 'message' => 'Ошибка добавления записи'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function byIdPlan($id)
    {
        return Service::where('plan_id', $id)->get();
    }

    public function planNotNull()
    {
        return Service::whereNotNull('plan_id')->get();
//        dd(Service::get());
    }
}
