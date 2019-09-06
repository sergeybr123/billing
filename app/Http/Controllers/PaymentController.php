<?php

namespace App\Http\Controllers;

use App\InvoiceOrder;
use App\Service;
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
//                    if($subscribe->plan_id != $plan->id) {

//                    }
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
//            return response()->json(['error' => 0, 'message' => $at_date]);
        } else {
            return response()->json(['error' => 1]);
        }
    }

    public function changeInvoice($invoice_id, $date_pay)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        if($invoice) {
            $invoice->paid = 1;
            $invoice->paid_at = $date_pay;
            $invoice->status = 'paid';
            $invoice->save();
        } else {
            return response()->json(['error' => 404, 'message' => 'Not found!']);
        }
    }

    public function changeSubscribe($invoice_id)
    {
        $invoice = Invoice::findOrFail($invoice_id);
        $subscribe = Subscribe::findOrFail($invoice->user_id);
        $plan = Plan::findOrFail($invoice->plan_id);
        if(!$subscribe) {
            $subscribe = new Subscribe();
            $subscribe->user_id = $invoice->user_id;
            $subscribe->plan_id = $plan->id;
        }
        if($subscribe->bot_count < $plan->bot_count) {
            $subscribe->bot_count = $plan->bot_count;
        }
        if($invoice->period < 12) {
            $subscribe->interval = "month";
        } else {
            $subscribe->interval = "year";
        }
        $subscribe->start_at = Carbon::today();
        if($invoice->type_id == 1) {

        } elseif ($invoice->type_id == 2) {
            $subscribe->end_at = Carbon::create($subscribe->start_at)->addMonths($invoice->period);
        }
        $subscribe->last_invoice = $invoice->id;
        $subscribe->active = 1;
        $subscribe->save();
    }

    public function getAmount($user_id, $start_at, $end_at, $subscribe_price, $subscribe_plan_id, $period, $plan_price, $plan_discount, $last_invoice)
    {
        if($last_invoice) {
            $invoice = Invoice::findOrFail($last_invoice);
        } else {
            $invoice = Invoice::where(['user_id' => $user_id, 'plan_id' => $subscribe_plan_id, 'status' => 'paid'])->orderBy('id', 'desc')->first();
        }
        $data_start = new Carbon($start_at);
        $date_end = new Carbon($end_at);
        $today = Carbon::now();
        $sub_days = $date_end->diff($data_start)->days + 1; // количество дней подписки
        $activ_days = $today->diff($data_start)->days; // количество использованных дней
        $not_use_sub = $date_end->diff($today)->days; // количество оставшихся дней
        $day_pay = $subscribe_price / 30;
        $sum_act_days = round($activ_days * $invoice->amount, 0);
        $isp_per = $sub_days - $activ_days;
        $per = $isp_per / $sum_act_days;

        if($period == 12) {
            $sum_plan = ($plan_price - ($plan_price * ($plan_discount / 100))) * 12;
        } else {
            $sum_plan = $plan_price * $period;
        }

        if($not_use_sub > 1) {
            $new_raschet_day_pay = round(($invoice->amount / $sub_days), 0);
            $new_isp_summa = round(($activ_days * $new_raschet_day_pay), 0);
            $new_prom_itogo = round(($invoice->amount - $new_isp_summa), 0);
            $amount = $sum_plan - $new_prom_itogo;
        } else {
            $amount = $sum_plan;
        }


        /*
         *  $date1 = new Carbon($subscribe->end_at);
            $date2 = Carbon::now();
            $days  = $date1->diff($date2)->days;
         * */

//        if ($days > 1) { // подписка активная
//            $in_day = ($subscribe_price / 30) * $days;
//            if ($period == 12) {
//                $price_plan = ($plan_price - ($plan_price * ($plan_discount / 100))) * 12;
//            } else {
//                $price_plan = $plan_price * $period;
//            }
//            $amount = $price_plan - $in_day;
//        } else { // подписка не активная
//            if ($period == 12) {
//                $amount = ($plan_price - ($plan_price * ($plan_discount / 100))) * 12;
//            } else {
//                $amount = $plan_price * $period;
//            }
//        }
        return $amount;
    }

    public function createInvoice(Request $request)
    {
        /*
         * user_id - обязательное
         * manager_id - ид менеджера создающий счет
         * invoice_id - не обязательное
         * type - тип подписки
         * plan_id - не обязательное
         * period - на сколько выставлен счет, не обязательное
         * bot_create-число создаваемых ботов, не обязательное
         * bot_count-число дополнительных ботов, не обязательное
         * */
        $user_id = $request->user_id;
        $manager_id = $request->manager_id;
        $type = $request->type;
        $subscribe = Subscribe::where('user_id', $user_id)->first();
        if ($request->plan_id) {
            $plan = Plan::findOrFail($request->plan_id);
        } else {
            $plan = Plan::findOrFail($subscribe->plan_id);
        }
        $bot = AdditionalSubscribesType::findOrFail(1);
        $service = Service::findOrFail(1);

        if ($request->invoice_id) { // если инвойс уже создан
            $inv = Invoice::findOrFail($request->invoice_id);
            if (is_null($inv->type_id)) {
                $inv->type_id = $type;
                $inv->save();
            }

//            return response()->json(['req' => $request->bot_create]);

            $invoice = Invoice::findOrFail($request->invoice_id);

            $ios = InvoiceOrder::where('invoice_id', $invoice->id)->get();
//            return response()->json(['i' => $ios]);
            // Если найден хоть один InvoiceOrder
            if(count($ios) >= 1) {
                $count_plan = 0;
                $io_plan_id = null;
                $count_service = 0;
                $io_service_id = null;
                $count_bot = 0;
                $io_bot_id = null;
                foreach ($ios as $io) {
                    if ($io->type === "plan") {
                        $count_plan++;
                        $io_plan_id = $io->id;
                    }
                    if ($io->type === "service") {
                        $count_service++;
                        $io_service_id = $io->id;
                    }
                    if ($io->type === "bot") {
                        $count_bot++;
                        $io_bot_id = $io->id;
                    }
                }
//                if(isset($request->period)) {
                    if($count_plan == 0) {
//                        return response()->json(['req' => $request->period]);
                        if($request->period) {
                            $amount = $this->getAmount($request->user_id, $subscribe->start_at, $subscribe->end_at, $subscribe->plans->price, $subscribe->plans->id, $request->period, $plan->price, $plan->discount, $subscribe->last_invoice);
                            // Создаем InvoiceOrder
                            $new_io = new InvoiceOrder();
                            $new_io->invoice_id = $invoice->id;
                            $new_io->type = 'plan';
                            $new_io->model = 'App\\Plan';
                            $new_io->paid_id = $plan->id;
                            $new_io->name = $plan->name;
                            $new_io->price = $plan->price;
                            $new_io->quantity = $request->period;
                            if ($request->period == 12) {
                                $new_io->discount = $plan->discount;
                            } else {
                                $new_io->discount = null;
                            }
                            $new_io->amount = $amount;
                            $new_io->save();
                        }
                    }
                    if($count_plan == 1){
                        if($request->period) {
                            $get_io = InvoiceOrder::findOrFail($io_plan_id);

                            $amount = $this->getAmount($request->user_id, $subscribe->start_at, $subscribe->end_at, $subscribe->plans->price, $subscribe->plans->id, $request->period, $plan->price, $plan->discount, $subscribe->last_invoice);

                            $get_io->paid_id = $plan->id;
                            $get_io->name = $plan->name;
                            $get_io->price = $plan->price;
                            $get_io->quantity = $request->period;
                            if ($request->period == 12) {
                                $get_io->discount = $plan->discount;
                            } else {
                                $get_io->discount = null;
                            }
                            $get_io->amount = $amount;
                            $get_io->save();
                        }
                        if(!$request->period){
                            $get_io = InvoiceOrder::findOrFail($io_plan_id);
                            $get_io->delete();
                        }

                    }
//                }
                if ($count_service == 0) {
                    if($request->bot_create) {
                        $order = new InvoiceOrder();
                        $order->invoice_id = $request->invoice_id;
                        $order->type = 'service';
                        $order->model = 'App\\Service';
                        $order->paid_id = $service->id;
                        $order->name = $service->name;
                        $order->price = $service->price;
                        $order->quantity = $request->bot_create;
                        if($request->period){
                            if($request->period == 12) {
                                $order->discount = $plan->discount;
                                $order->amount = ($service->price - ($service->price * ($plan->discount / 100))) * $request->bot_create;
                            } else {
                                $order->discount = null;
                                $order->amount = $service->price * $request->bot_create;
                            }
                        } else {
                            $order->discount = null;
                            $order->amount = $service->price * $request->bot_create;
                        }
                        $order->save();
                    }
                }
                if ($count_service == 1) {
                    if($request->bot_create) {
                        $get_ser_io = InvoiceOrder::findOrFail($io_service_id);
                        $get_ser_io->paid_id = $service->id;
                        $get_ser_io->name = $service->name;
                        $get_ser_io->price = $service->price;
                        $get_ser_io->quantity = $request->bot_create;
                        if($request->period) {
                            if ($request->period == 12) {
                                $get_ser_io->discount = $plan->discount;
                                $get_ser_io->amount = ($service->price - ($service->price * ($plan->discount / 100))) * $request->bot_create;
                            } else {
                                $get_ser_io->discount = null;
                                $get_ser_io->amount = $service->price * $request->bot_create;
                            }
                        } else {
                            $get_ser_io->discount = null;
                            $get_ser_io->amount = $service->price * $request->bot_create;
                        }

                        $get_ser_io->save();
                    }
                    if(!$request->bot_create) {
                        $get_ser_io = InvoiceOrder::findOrFail($io_service_id);
                        $get_ser_io->delete();
                    }
                }

                if ($count_bot == 0) {
                    if($request->bot_count) {
                        $date_end = new Carbon($subscribe->end_at);
                        $today = Carbon::now();
                        $not_use_sub = $date_end->diff($today)->days; // количество оставшихся дней
                        $in_month = ceil($not_use_sub / 30);

                        if($request->type == 1) {
                            if($request->bot_count > $subscribe->quantity_bot) {
                                $order = new InvoiceOrder();
                                $order->invoice_id = $request->invoice_id;
                                $order->type = 'bot';
                                $order->model = 'App\\AdditionalSubscribesType';
                                $order->paid_id = $bot->id;
                                $order->name = $bot->name;
                                $order->price = $bot->price;
                                $order->quantity = $request->bot_count - $subscribe->quantity_bot;
                                $order->discount = null;
                                $order->amount = ($bot->price * ($request->bot_count - $subscribe->quantity_bot)) * $in_month;
                                $order->save();
                            }
                        }
                        if($request->type == 2) {
                            if($request->bot_count > $plan->bot_count) {
                                $order = new InvoiceOrder();
                                $order->invoice_id = $request->invoice_id;
                                $order->type = 'bot';
                                $order->model = 'App\\AdditionalSubscribesType';
                                $order->paid_id = $bot->id;
                                $order->name = $bot->name;
                                $order->price = $bot->price;
                                $order->quantity = $request->bot_count - $plan->bot_count;
                                $order->discount = null;
                                $order->amount = ($bot->price * ($request->bot_count - $plan->bot_count)) * $in_month;
                                $order->save();
                            }
                        }
                    }
                }
                if ($count_bot == 1) {
                    if($request->bot_count) {
                        $get_bot_io = InvoiceOrder::findOrFail($io_bot_id);
                        $date_end = new Carbon($subscribe->end_at);
                        $today = Carbon::now();
                        $not_use_sub = $date_end->diff($today)->days; // количество оставшихся дней
                        $in_month = ceil($not_use_sub / 30);

//                        return response()->json([])

                        if($request->type == 1) {
                            if($request->bot_count > $subscribe->quantity_bot) {
                                $get_bot_io->paid_id = $bot->id;
                                $get_bot_io->name = $bot->name;
                                $get_bot_io->price = $bot->price;
                                $get_bot_io->quantity = $request->bot_count - $subscribe->quantity_bot;
                                $get_bot_io->discount = null;
                                $get_bot_io->amount = ($bot->price * ($request->bot_count - $subscribe->quantity_bot)) * $in_month;
                                $get_bot_io->save();
                            }
                        }
                        if($request->type == 2) {
                            if($request->bot_count > $plan->bot_count) {
                                $get_bot_io->paid_id = $bot->id;
                                $get_bot_io->name = $bot->name;
                                $get_bot_io->price = $bot->price;
                                $get_bot_io->quantity = $request->bot_count - $plan->bot_count;
                                $get_bot_io->discount = null;
                                $get_bot_io->amount = ($bot->price * ($request->bot_count - $plan->bot_count)) * $in_month;
                                $get_bot_io->save();
                            }
                        }
                    }
                    if(!$request->bot_count) {
                        $get_ser_io = InvoiceOrder::findOrFail($io_bot_id);
                        $get_ser_io->delete();
                    }
                }





            }
            // Если не найдено ни одного InvoiceOrder
            if(count($ios) == 0){
                if ($request->period) {
                    $amount = $this->getAmount($request->user_id, $subscribe->start_at, $subscribe->end_at, $subscribe->plans->price, $subscribe->plans->id, $request->period, $plan->price, $plan->discount, $subscribe->last_invoice);
//                    return response()->json(['days' => $amount]);
                    // Создаем InvoiceOrder
                    $new_io = new InvoiceOrder();
                    $new_io->invoice_id = $request->invoice_id;
                    $new_io->type = 'plan';
                    $new_io->model = 'App\\Plan';
                    $new_io->paid_id = $plan->id;
                    $new_io->name = $plan->name;
                    $new_io->price = $plan->price;
                    $new_io->quantity = $request->period;
                    if ($request->period == 12) {
                        $new_io->discount = $plan->discount;
                    } else {
                        $new_io->discount = null;
                    }
                    $new_io->amount = $amount;
                    $new_io->save();
                }
                if($request->bot_create) {
                    $order = new InvoiceOrder();
                    $order->invoice_id = $request->invoice_id;
                    $order->type = 'service';
                    $order->model = 'App\\Service';
                    $order->paid_id = $service->id;
                    $order->name = $service->name;
                    $order->price = $service->price;
                    $order->quantity = $request->bot_create;
                    if($request->period) {
                        if($request->period == 12) {
                            $order->discount = $plan->discount;
                            $order->amount = ($service->price - ($service->price * ($plan->discount / 100))) * $request->bot_create;
                        } else {
                            $order->discount = null;
                            $order->amount = $service->price * $request->bot_create;
                        }
                    } else {
                        $order->discount = null;
                        $order->amount = $service->price * $request->bot_create;
                    }
                    $order->save();
                }
                if($request->bot_count) {

                    $date_end = new Carbon($subscribe->end_at);
                    $today = Carbon::now();
                    $not_use_sub = $date_end->diff($today)->days; // количество оставшихся дней
                    $in_month = ceil($not_use_sub/30);
//                    $price_bot

//                    return response()->json(['sum' => ((($request->bot_count - $subscribe->quantity_bot) * $bot->price)*$in_month)]);

                    if($request->type == 1) {
                        if($request->bot_count > $subscribe->quantity_bot) {
                            $order = new InvoiceOrder();
                            $order->invoice_id = $request->invoice_id;
                            $order->type = 'bot';
                            $order->model = 'App\\AdditionalSubscribesType';
                            $order->paid_id = $bot->id;
                            $order->name = $bot->name;
                            $order->price = $bot->price;
                            $order->quantity = $request->bot_count - $subscribe->quantity_bot;
                            $order->discount = null;
                            $order->amount = ($bot->price * ($request->bot_count - $subscribe->quantity_bot)) * $in_month;
                            $order->save();
                        }
                    }
                    if($request->type == 2) {
                        if($request->bot_count > $plan->bot_count) {
                            $order = new InvoiceOrder();
                            $order->invoice_id = $request->invoice_id;
                            $order->type = 'bot';
                            $order->model = 'App\\AdditionalSubscribesType';
                            $order->paid_id = $bot->id;
                            $order->name = $bot->name;
                            $order->price = $bot->price;
                            $order->quantity = $request->bot_count - $plan->bot_count;
                            $order->discount = null;
                            $order->amount = ($bot->price * ($request->bot_count - $plan->bot_count)) * $in_month;
                            $order->save();
                        }
                    }
                }
            }
            return Invoice::where('id', $request->invoice_id)->with('orders')->first();
        } else { // инвойса нету
            // Создаем инвойс и возвращаем его
            $invoice = new Invoice();
            $invoice->user_id = $request->user_id;
            if ($request->manager_id) {
                $invoice->manager_id = $request->manager_id;
            }
            if ($request->type) {
                $invoice->type_id = $request->type;
            }
            $invoice->amount = 0.00;
            $invoice->status = 'active';
            $invoice->save();

            return Invoice::findOrFail($invoice->id);
        }
    }


}
