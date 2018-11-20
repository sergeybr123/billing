<?php

namespace App\Http\Controllers;

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

        if($request->date == null) {
            $date = Carbon::now();
        } else {
            $date = $request->date;
        }

        $invoice->paid = true;
        $invoice->paid_at = Carbon::now();
        $invoice->save();

        $subscribe->plan_id = $plan->id;
        $subscribe->interval = $plan->interval;
        $subscribe->start_at = $date;
        if($plan->interval == 'month') {
            $subscribe->end_at = Carbon::parse($date)->addMonth();
        } elseif($plan->interval == 'year') {
            $subscribe->end_at = Carbon::parse($date)->addYear();
        }
        $subscribe->active = true;
        $subscribe->save();

        if($invoice != null && $subscribe != null) {
            return response()->json(['error' => 0]);
        } else {
            return response()->json(['error' => 1]);
        }
    }


    public function set_not_active()
    {
        $subscribes = Subscribe::where('end_at', '<=', Carbon::today())->where('active', 1)->get();
        foreach ($subscribes as $subscribe) {
            $subscribe->active = 0;
            $subscribe->save();
        }
        try{
            return response()->json(['error' => 0, 'subscribes' => $subscribes]);
        }
        catch(Throwable $t) {
            return response()->json(['error' => 1, 'message' => $t]);
        }
    }
}
