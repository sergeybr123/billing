<?php

namespace App\Http\Controllers;

use App\Invoice;
use Illuminate\Http\Request;
use App\Invoice as InvoiceModel;
use App\Http\Resources\Invoice as InvoiceResource;
use App\Http\Resources\InvoicesCollection;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return InvoiceResource::collection(InvoiceModel::whereNull('deleted_at')->orderBy('id', 'desc')->paginate(20));//InvoiceModel::with('types')->orderBy('id', 'desc')->paginate(30));
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
        $inv = InvoiceModel::create($request->all());
        if($inv) {
            return $inv;
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
        return new InvoiceResource(InvoiceModel::find($id));
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
        $invoice = InvoiceModel::findOrFail($id);
        try {
            $invoice->update($request->all());
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
        //
    }


    // Ручная установка, что счет оплачен
    public function paid($id)
    {
        $invoice = InvoiceModel::find($id);
        if($invoice) {
            if($invoice->type_id == 1) {

            }
        } else {
            return ['error' => 1, 'message' => 'Счет не найден'];
        }
    }


    // Получаем все счета по ИД пользователя
    public function userInvoice($id)
    {
        return InvoiceResource::collection(InvoiceModel::where('user_id', $id)->whereNull('deleted_at')->orderBy('id', 'desc')->paginate(20));
    }

    // Общее количество счетов
    public function countInvoice()
    {
        $count = InvoiceModel::whereNull('deleted_at')->count();
        return $count;
    }

    public function completed($id)
    {
        $invoice = Invoice::findOrFail($id);
        if($invoice) {
            $invoice->status = 'completed';
            $invoice->save();
            return response()->json(['error' => 0, 'status' => 'completed']);
        } else {
            return response()->json(['error' => 1]);
        }
    }
}
