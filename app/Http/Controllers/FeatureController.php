<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Feature;
use App\Http\Resources\Feature as FeatureResource;
use App\Http\Resources\FeatureCollection;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $features = Feature::with('children')->with('parents')->paginate(30);
        return new FeatureCollection($features);
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
        $code = Feature::where('code', $request->code)->first();
        if ($code != null) {
            return ['error' => 1, 'message' => 'Запись с таким кодом уже существует'];
        }
        else {
            try {
                Feature::create($request->all());
                return ['error' => 0, 'message' => 'Запись успешно добавлена'];
            } catch (Exception $e) {
                return ['error' => 1, 'message' => 'Ошибка добавления записи', 'text' => $e->getMessage()];
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
        return new FeatureResource(Feature::find($id));
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
        $feature = Feature::findOrFail($id);
        try {
            $feature->update($request->all());
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
        $feature = Feature::findOrFail($id);
        $feature->delete();
        try {
            return ['error' => 0, 'message' => 'Запись успешно удалена'];
        } catch (Exception $e) {
            return ['error' => 1, 'message' => 'Запись не найдена', 'text' => $e->getMessage()];
        }
    }
}
