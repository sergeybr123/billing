<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Subscribe;
use App\Http\Resources\Subscribe as SubscribeResource;
use App\Http\Resources\SubscribesCollection;
use App\AdditionalSubscribe;
use App\Plan;
use App\Invoice;

use Carbon\Carbon;

class SubscribeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubscribeResource::orderBy('id', 'desc')->with('plans')->paginate(30);//paginate(30));
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
        Subscribe::create($request->all());
        try {
            return ['error' => 0, 'message' => 'Запись успешно добавлена'];
        } catch (ParseError $t) {
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
        $subsc = Subscribe::where('user_id', $id)->first();
        if($subsc != null) {
//            if($subsc->active != 0) {
                return new SubscribeResource($subsc);
//            } else {
//                return ['error' => 1, 'message' => 'Подписка не активна'];
//            }
        } else {
            return ['error' => 1, 'message' => 'Запись не найдена'];
        }

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
        //
    }

    // Переподписка пользователя после оплаты
    public function rewrite(Request $request, $user_id, $plan_id, $period)
    {
        $subscribe = Subscribe::where('user_id', $user_id)->first();
        $plan = Plan::find($plan_id);
        if($subscribe) {
            $dt_end = $subscribe->end_at;
            $additional = AdditionalSubscribe::where('subscribe_id', $subscribe->id)->get();
        } else {
            return ['error' => 1, 'message' => 'Подписка не найдена'];
        }
    }

    // Бесплатная подписка для регистрации пользователя
//    public function setTrial($id)
//    {
//        $plan = Plan::where('code', 'trial')->first();
//        $subscribe = Subscribe::where('user_id', $id)->first();
//        if($subscribe == null) {
//            $us_sub = Subscribe::create([
//                    'user_id' => $id,
//                    'plan_id' => $plan->id,
//                    'interval' => $plan->interval,
//                    'term' => $plan->period.' days',
//                    'quantity_bot' => $plan->bot_count,
//                    'start_at' => Carbon::today(),
//                    'end_at' => Carbon::today()->addDays($plan->period),
//                    'active' => 1,
//                ]);
//            if($us_sub) {
//                return ['error' => 0, 'message' => 'Пользователь подписан на пакет Trial'];
//            } else {
//                return ['error' => 1, 'message' => 'Ошибка подписки пользователя'];
//            }
//        } else {
//            return ['error' => 1, 'message' => 'Данный пользователь уже подписан'];
//        }
//    }

    // Подписка на пакет Unlimited
    public function unlimited($id)
    {
        $plan = Plan::where('code', 'unlimited')->first();
        $subscribe = Subscribe::where('user_id', $id)->first();
        if($subscribe == null) {
            $us_sub = Subscribe::create([
                'user_id' => $id,
                'plan_id' => $plan->id,
                'interval' => $plan->interval,
                'quantity_bot' => $plan->bot_count,
                'start_at' => Carbon::now(),
                'active' => 1,
            ]);
            if($us_sub) {
                return ['error' => 0, 'message' => 'Пользователь подписан на пакет "Unlimited"'];
            } else {
                return ['error' => 1, 'message' => 'Ошибка подписки пользователя'];
            }
        } else {
            return ['error' => 1, 'message' => 'Данный пользователь уже подписан'];
        }
    }

    // Активации подписки после оплаты пользователя
    public function activate($user_id)
    {
        $subscribe = Subscribe::where('user_id', $user_id)->first();
        if(!$subscribe) {
            $us_sub = Subscribe::create([
                'active' => 1,
            ]);
            if($us_sub) {
                return ['error' => 0, 'message' => 'Пользователь подписан на бесплатный пакет'];
            } else {
                return ['error' => 1, 'message' => 'Ошибка подписки пользователя'];
            }
        } else {
            return ['error' => 1, 'message' => 'Данный пользователь уже подписан'];
        }
    }

    // Продление подписки пользователя
    public function extSubscribe($id) {
        $subscribe = Subscribe::where('user_id', $id)->first();

        // Получаем все не оплаченные счета пользователя
        $inv = Invoice::where('user_id', $id)->where('paid', 0)->where('type_id', 1)->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->orderBy('id', 'desc')->first();
        if($inv == null) {
//            $ninv = new Invoice();
//            $ninv->user_id = $id;
//            $ninv->amount = $subscribe->plans->price;
//            $ninv->type_id = 1;
//            $ninv->plan_id = $subscribe->plans->id;
//            if($subscribe->plans->interval == 'month') {
//                // Делаем продление на один месяц
//                $ninv->period = 1;
//            }
//            $ninv->service_id = null;
//            $ninv->description = null;
//            $ninv->paid = 0;
//            $ninv->paid_at = null;
//            $ninv->save();
            return response()->json(['error' => 1, 'message' => 'Счет не найден']);
        } else {
            return $inv;
        }
    }


    /*--------Подписка на новый тарифный план--------*/
    public function new_plan(Request $request)
    {
        $subscribe = Subscribe::where('user_id', $request->user_id)->first(); // Получаем подписку пользователя

        $plan = Plan::findOrFail($subscribe->plan_id); //Получаем план пользователя

        //Высчитываем неиспользованное количество дней
        $endDate = Carbon::parse($subscribe->end_at);
        $countDate = $endDate->diffInDays();

        if($subscribe->interval == 'month') {
            $planPriceDay = round($plan->price / 30); // Высчитываем стоимость тарифного плана в день
        } else {
            $planPriceDay = round(($plan->price - ($plan->price*($plan->discount / 100))) / 30); // Высчитываем стоимость тарифного плана в день в зависимости от скидки
        }
        return response()->json(['request' => $request]);
    }

    // При регистрации даем подписку FREE
    public function setFreeSubscribe($user_id)
    {
        $free = Plan::where('code', 'free')->first();
        $subscribe = new Subscribe();
        $subscribe->user_id = $user_id;
        $subscribe->plan_id = $free->id;
        $subscribe->interval = $free->interval;
        $subscribe->bot_count = $free->bot_count;
        $subscribe->start_at = Carbon::now();
        $subscribe->active = 1;
        $subscribe->save();
        return response()->json(['error' => 0, 'message' => 'Пользователь успешно добавлен']);
    }

    public function setTrialSubscribe($user_id)
    {
        $plan = Plan::where('code', 'trial')->first();
        $subscribe = new Subscribe();
        $subscribe->user_id = $user_id;
        $subscribe->plan_id = $plan->id;
        $subscribe->interval = $plan->interval;
        $subscribe->term = $plan->period.' days';
        $subscribe->bot_count = $plan->bot_count;
        $subscribe->start_at = Carbon::today();
        $subscribe->end_at = Carbon::today()->addDays($plan->period);
        $subscribe->active = 1;
        $subscribe->save();
        return response()->json(['error' => 0, 'message' => 'Пользователь успешно добавлен']);
    }

    public function extend_subscribe($user_id, $plan_id, $period)
    {

    }
}
