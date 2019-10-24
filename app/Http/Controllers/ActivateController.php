<?php

namespace App\Http\Controllers;

use App\SubscriptionHistory;
use Illuminate\Http\Request;
use App\Invoice;
use App\Subscribe;
use App\Plan;
use Carbon\Carbon;
use Throwable;

class ActivateController extends Controller
{

    public function activate(Request $request)
    {
        $subscribe = Subscribe::where('user_id', $request->user_id)->first();
        $invoice = Invoice::find($request->invoice_id);
        $plan = Plan::find($invoice->plan_id);

        if ($request->date == null) {
            $date = Carbon::now();
        } else {
            $date = $request->date;
        }

        if($invoice) {
            if($invoice->paid == 0 && $invoice->paid_at == null) {
                $invoice->paid = true;
                $invoice->paid_at = Carbon::now();
                $invoice->save();
            }
        }

        if($subscribe != null) {
            $subscribe->plan_id = $plan->id;
            $subscribe->interval = $plan->interval;
            if($invoice->type_id == 1) {
                // если продление подписки
                if ($plan->interval == 'month') {
                    if($invoice->period != null) {
                        $subscribe->end_at = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                    } else {
                        $subscribe->end_at = Carbon::parse($subscribe->end_at)->addMonth();
                    }
                } elseif ($plan->interval == 'year') {
                    $subscribe->end_at = Carbon::parse($subscribe->end_at)->addYear();
                }
            } else {
                // если подписка
                $subscribe->start_at = $date;
                if ($plan->interval == 'month') {
                    $subscribe->end_at = Carbon::parse($date)->addMonths($invoice->period);
                } elseif ($plan->interval == 'year') {
                    $subscribe->end_at = Carbon::parse($date)->addYear();
                }
            }

            $subscribe->active = true;
            $subscribe->save();
        } else {
            $subscribe = new Subscribe();
            $subscribe->user_id = $request->user_id;
            $subscribe->plan_id = $plan->id;
            $subscribe->interval = $plan->interval;
            $subscribe->start_at = $date;
            if ($plan->interval == 'month') {
                if($invoice->period != null) {
                    $subscribe->end_at = Carbon::parse($subscribe->end_at)->addMonths($invoice->period);
                } else {
                    $subscribe->end_at = Carbon::parse($subscribe->end_at)->addMonth();
                }
            } elseif ($plan->interval == 'year') {
                $subscribe->end_at = Carbon::parse($date)->addYear();
            }
            $subscribe->active = true;
            $subscribe->save();
        }

        if ($invoice != null && $subscribe != null) {
            return response()->json(['error' => 0]);
        } else {
            return response()->json(['error' => 1]);
        }
    }

    public function set_not_active()
    {
        $subscribes = Subscribe::where('end_at', '<=', Carbon::today()->subDay())->where('active', 1)->get();
        foreach ($subscribes as $subscribe) {
            $subscribe->active = 0;
            $subscribe->save();

            /*--------Вставить код для отправки в amoCRM--------*/
        }
        try {
            return response()->json(['error' => 0, 'subscribes' => $subscribes]);
        } catch (Throwable $t) {
            return response()->json(['error' => 1, 'message' => $t]);
        }
    }

    // Ставим план Free у кого закончилась подлписки от 24.10.2019
    public function set_free_not_active()
    {
        $subscribes = Subscribe::where('end_at', '<=', Carbon::today()->subDays(1))->where('active', true)->get();
        $free = Plan::where('code', 'free')->first();
        foreach ($subscribes as $item) {
            $item->plan_id = $free->id;
            $item->interval = 'unlimited';
            $item->term = 'unlimited';
            $item->start_at = Carbon::today();
            $item->end_at = Carbon::today()->addDays($free->period);
            $item->active = true;
            $item->save();
            // Записываем историю
            $this->writeSubscribeHistory($item->id, Carbon::today()->addDays($free->period));
        }
        try {
            return response()->json(['error' => 0, 'subscribes' => $subscribes]);
        } catch (\Exception $e) {
            return response()->json(['error' => 1]);
        }
    }

    // Добавление записи в историю подписки от 24.10.2019
    public function writeSubscribeHistory($subscribe_id, $plan_id, $term = 0)
    {
        $plan = Plan::findOrFail($plan_id);

        $hi = new SubscriptionHistory();
        $hi->subscribe_id = $subscribe_id;
        $hi->type = 'App\\Plan';
        $hi->plan_id = $plan->id;
        $hi->start = Carbon::today();
        if($plan->period > 0) {
            $hi->end = Carbon::today()->addDays($term);
        } else {
            $hi->end = null;
        }
        $hi->save();
    }

}
