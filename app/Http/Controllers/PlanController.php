<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Plan;
use App\Http\Resources\Plan as PlanResource;
use App\Http\Resources\PlansCollection;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        return new PlansCollection(Plan::with('features')->get());
        return PlanResource::collection(Plan::where('code', '!=', 'unlimited')->get());
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
        $code = Plan::where('code', $request->code)->first();
        if ($code != null) {
            return ['error' => 1, 'message' => 'Запись с таким кодом уже существует'];
        }
        else {
            try {
                Plan::create($request->all());
                return ['error' => 0, 'message' => 'Запись успешно добавлена'];
            } catch (ParseError $t) {
                return ['error' => 1, 'message' => 'Ошибка добавления записи'];
            }
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
        return new PlanResource(Plan::find($id)->where('code', '!=', 'unlimited'));
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
        $plan = Plan::findOrFail($id);
        try {
            $plan->update($request->all());
            try {
                return ['error' => 0, 'message' => 'Запись успешно обнавлена'];
            } catch (Exception $e) {
                return ['error' => 1, 'message' => 'Ошибка обновления записи', 'text' => $e->getMessage()];
            }
        } catch (Exception $e) {
            return ['error' => 1, 'message' => 'Запись не найдена', 'text' => $e->getMessage()];
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
        $plan = Plan::findOrFail($id);
        $plan->delete();
        try {
            return ['error' => 0, 'message' => 'Запись успешно удалена'];
        } catch (Exception $e) {
            return ['error' => 1, 'message' => 'Запись не найдена', 'text' => $e->getMessage()];
        }
    }

    public function all()
    {
        return PlanResource::collection(Plan::orderBy('sort_order')->get());
    }
}
