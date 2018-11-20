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
        return new SubscribesCollection(Subscribe::orderBy('id', 'desc')->with('plans')->paginate(30));//paginate(30));
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
        $subsc = Subscribe::where('user_id', $id)->with('additional')->first();
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
    public function free($id)
    {
        $plan = Plan::where('code', 'free')->first();
        $subscribe = Subscribe::where('user_id', $id)->first();
        if($subscribe == null) {
            $us_sub = Subscribe::create([
                    'user_id' => $id,
                    'plan_id' => $plan->id,
                    'interval' => $plan->interval,
                    'start_at' => Carbon::now(),
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
        $inv = Invoice::where('user_id', $id)->where('paid', 0)->where('type_id', 1)->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()])->first();
        if($inv == null) {
            $ninv = new Invoice();
            $ninv->user_id = $id;
            $ninv->amount = $subscribe->plans->price;
            $ninv->type_id = 1;
            $ninv->plan_id = $subscribe->plans->id;
            $ninv->service_id = null;
            $ninv->description = null;
            $ninv->paid = 0;
            $ninv->paid_at = null;
            $ninv->save();
            return $ninv;
        } else {
            return $inv;
        }
    }
}
