<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CPLog;
use App\Invoice;
use App\Subscribe;
use App\Plan;

use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
//        $invoice = Invoice::findOrFail((int)$request->input('InvoiceId'));
//
//        $code = 11;
//
//        if ($invoice->amount == (int)$request->input('Amount') && !$invoice->paid)
//        {
//            $invoice->payment->pay();
//
//            CPLog::create([
//                'invoice_id' => $request->input('InvoiceId'),
//                'transaction_id' => $request->input('TransactionId'),
//                'currency' => $request->input('Currency'),
//                'cardFirstSix' => $request->input('CardFirstSix'),
//                'cardLastFour' => $request->input('CardLastFour'),
//                'cardType' => $request->input('CardType'),
//                'name' => $request->input('Name'),
//                'email' => $request->input('Email'),
//                'issuer' => $request->input('Issuer'),
//                'token' => $request->input('Token'),
//            ]);
////            $invoice->cplog()->save($cplog);
//
//            $invoice->paid_on = $request->input('DateTime');
//            $invoice->paid = TRUE;
//            $invoice->save();
//
//            $code = 0;
//
//        }
//
//
//        return $code;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscribe = Subscribe::findOrFail($id);
        $subscribe->delete();
        try {
            return response()->json(['error' => 0, 'message' => 'Запись успешно удалена']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 1, 'message' => $th]);
        }
    }

    /*----------Принимаем ответ от CloudPayment------------*/
    public function pays(Request $request)
    {
        $code = 1;

        if(strlen($request->InvoiceId) <6) {
            Storage::put($request->InvoiceId.'.txt', $request);
            $code = 0;
        } else {
            $invoice = Invoice::findOrFail($request->InvoiceId);

            if($invoice->amount == $request->Amount) {
                CPLog::updateOrCreate([
                    'invoice_id' => $request->InvoiceId,
                    'transaction_id' => $request->TransactionId,
                    'currency' => $request->Currency,
                    'cardFirstSix' => $request->CardFirstSix,
                    'cardLastFour' => $request->CardLastFour,
                    'cardType' => $request->CardType,
                    'name' => $request->Name,
                    'email' => $request->Email,
                    'issuer' => $request->Issuer,
                    'token' => $request->Token,
                ]);
                $invoice->paid = true;
                $invoice->paid_at = $request->DateTime;
                $invoice->save();

                $subscribe = Subscribe::where('user_id', $invoice->user_id)->first();
                $plan = Plan::findOrFail($invoice->plan_id);
                $interval = $plan->interval;

                /*-----------Если нет подписки создаем-----------*/
                if($subscribe == null) {
                    $subscribe = new Subscribe();
                    $subscribe->plan_id = $plan->id;
                    $subscribe->interval = $interval;
                    $subscribe->start_at = Carbon::now();
                }

                /*------------Если продление подписки-------------*/
                if($invoice->type_id == 1) {
                    if(Carbon::parse($subscribe->end_at)->format('d.m.Y') < Carbon::parse($request->DateTime)->format('d.m.Y')) {
                        $subscribe->start_at = Carbon::now();
                        if($interval == 'month') {
//                            $dt = Carbon::now()->addMonths($invoice->period);
                            if($invoice->period != null) {
                                $dt = Carbon::now()->addMonths($invoice->period);
                            } else {
                                $dt = Carbon::now()->addMonth();
                            }
                        }
                        if($interval == 'year') {
                            $dt = Carbon::now()->addYear();
                        }
                    } else {
                        if($interval == 'month') {
//                            $dt = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                            if($invoice->period != null) {
                                $dt = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                            } else {
                                $dt = Carbon::parse($subscribe->end_at)->addMonth();
                            }
                        }
                        if($interval == 'year') {
                            $dt = Carbon::parse($subscribe->end_at)->addYear();
                        }
                    }
                    $subscribe->end_at = $dt;
                    $subscribe->active = true;
//                    $subscribe->save();
                }
                /*----------------Если переподписка на новый тариф-------------------*/
                if($invoice->type_id == 2) {
                    if($interval == 'month') {
                        $dt = Carbon::now()->addMonths($invoice->period);
                    }
                    if($interval == 'year') {
                        $dt = Carbon::now()->addYear();
                    }
                    $subscribe->plan_id = $plan->id;
                    $subscribe->interval = $interval;
                    $subscribe->start_at = Carbon::now();
                    $subscribe->end_at = $dt;
                    $subscribe->active = true;
//                    $subscribe->save();
                }
                /*-----Сохраняем подписку------*/
                $subscribe->save();
                /*-----Записываем код для возврата GetChat------*/
                $code = 0;
            }
        }
        /*-----------Возвращаем в GetChat---------*/
        return response()->json(['error' => $code]);
    }

    /*---------Выставляем оплату инвойса в ручную--------*/
    public function payWithDay(Request $request)
    {
        $invoice = Invoice::findOrFail($request->id);
        if($invoice) {
            $invoice->paid = 1;
            $invoice->paid_at = $request->date;
            $invoice->save();
            return response()->json(['error' => 0, 'message' => $invoice]);
        } else {
            return response()->json(['error' => 1]);
        }
    }
}
