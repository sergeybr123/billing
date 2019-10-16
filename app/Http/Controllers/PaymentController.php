<?php

namespace App\Http\Controllers;

use App\InvoiceOrder;
use App\Service;
use App\SubscriptionHistory;
use Illuminate\Http\Request;
use App\CPLog;
use App\Invoice;
use App\Subscribe;
use App\Plan;
use App\User;
use App\AdditionalSubscribesType;
use \App\Http\Resources\Invoice as InvoiceResource;

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
     * @param  \Illuminate\Http\Request $request
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
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

        if (strlen($request->InvoiceId) < 6) {
            Storage::put($request->InvoiceId . '.json', json_decode($request));
            $code = 0;
        } else {
            $invoice = Invoice::findOrFail($request->InvoiceId);

            if ($invoice->amount == $request->Amount) {
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

//                Раскомментировать позже, после реф
//                $this->changeInvoice($invoice->id, $request->DateTime);
//                if(in_array($invoice->ytpe, [1, 2, 4])) {
//                    $this->changeSubscribe($invoice->id);
//                }

//                Закоментировать позже, после реф
                $invoice->paid = true;
                $invoice->paid_at = $request->DateTime;
                $invoice->status = 'paid';
                $invoice->save();

                if ($invoice->type_id == 1 || $invoice->type_id == 2) {
                    $subscribe = Subscribe::where('user_id', $invoice->user_id)->first();
                    $plan = Plan::findOrFail($invoice->plan_id);
                    $interval = $plan->interval;

                    /*-----------Если нет подписки создаем-----------*/
                    if ($subscribe == null) {
                        $subscribe = new Subscribe();
                        $subscribe->user_id = $invoice->user_id;
                        $subscribe->plan_id = $plan->id;
                        $subscribe->interval = $interval;
                        $subscribe->start_at = Carbon::now();
                    }

                    /*------------Если продление подписки-------------*/
                    if ($invoice->type_id == 1) {
                        if (Carbon::parse($subscribe->end_at) < Carbon::parse($request->DateTime)) {
                            $subscribe->start_at = Carbon::now();
                            if ($interval == 'month') {
//                            $dt = Carbon::now()->addMonths($invoice->period);
                                if ($invoice->period != null) {
                                    $dt = Carbon::now()->addMonths($invoice->period);
                                } else {
                                    $dt = Carbon::now()->addMonth();
                                }
                            }
//                            if($interval == 'year') {
//                                $dt = Carbon::now()->addYear();
//                            }
                        } else {
                            if ($interval == 'month') {
                                if ($invoice->period != null) {
                                    $dt = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                                } else {
                                    $dt = Carbon::parse($subscribe->end_at)->addMonth();
                                }
                            }
//                            if($interval == 'year') {
//                                $dt = Carbon::parse($subscribe->end_at)->addYear();
//                            }
                        }
                        $subscribe->end_at = $dt;
                        $subscribe->active = true;
//                    $subscribe->save();
                    }
                    /*----------------Если переподписка на новый тариф-------------------*/
                    if ($invoice->type_id == 2) {
                        if ($interval == 'month') {
                            if ($invoice->period != null) {
                                $dt = Carbon::now()->addMonths($invoice->period);
                            } else {
                                $dt = Carbon::now()->addMonth();
                            }
                        }
//                        if($interval == 'year') {
//                            $dt = Carbon::now()->addYear();
//                        }
//                        if($subscribe->plan_id == 8 && $plan->id != 8) {
//
//                        }
                        $subscribe->plan_id = $plan->id;
                        if($invoice->period < 12) {
                            $subscribe->interval = "month";
                        } else {
                            $subscribe->interval = "year";
                        }
                        $subscribe->start_at = Carbon::now();
                        $subscribe->end_at = $dt;
                        $subscribe->active = true;
//                    $subscribe->save();
                    }
                    /*-----Сохраняем подписку------*/
                    $subscribe->last_invoice = $invoice->id;
                    $subscribe->save();
                }
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
        $at_date = $request->date;
        $invoice = Invoice::findOrFail($request->id);


        if ($invoice) {
//            Раскомментировать позже, после реф
//            $this->changeInvoice($invoice->id, $at_date);
//            if(in_array($invoice->ytpe, [1, 2, 4])) {
//                $this->changeSubscribe($invoice->id);
//            }

//            Закомментировать позже, после реф
            $invoice->paid = 1;
            $invoice->paid_at = $at_date;
            $invoice->status = 'paid';
            $invoice->save();

            if ($invoice) {
                $subscribe = Subscribe::where('user_id', $invoice->user_id)->first();
                $plan = Plan::findOrFail($invoice->plan_id);
                if (is_null($subscribe)) {
                    $subscribe = new Subscribe();
                    $subscribe->user_id = $invoice->user_id;
                    $subscribe->plan_id = $plan->id;
                    if($invoice->period < 12) {
                        $subscribe->interval = "month";
                    } else {
                        $subscribe->interval = "year";
                    }
                }
                // Продление
                $subscribe->plan_id = $plan->id;
                if ($invoice->type_id == 1) {
                    if ($subscribe->end_at < $at_date) {
                        $subscribe->start_at = Carbon::parse($at_date);
                        $subscribe->end_at = Carbon::parse($at_date)->addMonths($invoice->period);
                    }
                    if ($plan->interval == 'month') {
                        $subscribe->end_at = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                    }
//                    else {
//                        $subscribe->end_at = Carbon::parse($subscribe->end_at)->addYear();
//                    }
                } // Подписка
                elseif ($invoice->type_id == 2) {
                    $subscribe->start_at = Carbon::parse($at_date)->format('Y-m-d');
                    if ($plan->interval == 'month') {
                        $subscribe->end_at = Carbon::parse($at_date)->addMonths($invoice->period)->format('Y-m-d');
                    } else {
                        $subscribe->end_at = Carbon::parse($at_date)->addYear()->format('Y-m-d');
                    }
                }
                $subscribe->last_invoice = $invoice->id;
                $subscribe->active = 1;
                $subscribe->save();
            }

            return response()->json(['error' => 0, 'message' => $invoice]);
        } else {
            return response()->json(['error' => 1]);
        }
    }

    // Обработка инвойса после поступления платежа от шлюза
    public function changeInvoice($invoice_id, $date_pay)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        if($invoice) {
            $invoice->paid = 1;
            $invoice->paid_at = $date_pay;
            $invoice->status = 'paid';
            $invoice->save();
            try {
                $error = 0;
            } catch (\Exception $e) {
                $error = 1;
            }
            return response()->json(['error' => $error]);
        } else {
            return response()->json(['error' => 404, 'message' => 'Not found!']);
        }
    }

    // Обрабока подписки после платежа
    public function changeSubscribe($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $invoice_ref_details = $invoice->ref_invoice->details;
        $subscribe = Subscribe::findOrFail($invoice->user_id);
        $plan_sub = Plan::findOrFail($subscribe->plan_id);

        $invoice_item_plan = null;
        $subscribe_item_bot = null;
        foreach ($invoice_ref_details as $detail) {
            if($detail->type === 'plan') {
                $invoice_item_plan = $detail;
            }
            if($detail->type === 'bot') {
                $subscribe_item_bot = $detail;
            }
        }
        if(!$subscribe) {
            $subscribe = new Subscribe();
            $subscribe->user_id = $invoice->user_id;
            $subscribe->plan_id = $invoice_item_plan->plan_id;
        }
        if($invoice->type_id == 2 && $subscribe->plan_id != $invoice_item_plan->paid_id) {
            $subscribe->plan_id = $invoice_item_plan->paid_id;
            $sub_bot = 0;
            if($subscribe->bot_count > $plan_sub->bot_count)
            {
                $sub_bot = $subscribe->bot_count - $plan_sub->bot_count;
                $subscribe->bot_count = $plan_sub->bot_count;
            }
            if($subscribe->bot_count < $invoice_item_plan->bot_count) {
                $subscribe->bot_count = ($invoice_item_plan->bot_count + $sub_bot);
            }
        }
        if($subscribe_item_bot) {
            $subscribe->bot_count += $subscribe_item_bot->quantity;
        }
        if($invoice->period < 365) {
            $subscribe->interval = "month";
        } else {
            $subscribe->interval = "year";
        }
        $subscribe->start_at = $invoice->paid_at;
        $subscribe->end_at = $invoice->paid_at->addDays($invoice->period);
        $subscribe->last_invoice = $invoice->id;
        $subscribe->active = 1;
        $subscribe->save();

        // Записываем в историю
        $history = new SubscriptionHistory();
        $history->subscribe_id = $subscribe->id;
        $history->type = "App\\Subscribe";
        $history->plan_id = $plan_sub->id;
        $history->start = $invoice->paid_at;
        $history->end = $invoice->paid_at->addDays($invoice->period);
        $history->save();
    }
}
