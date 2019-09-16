<?php

namespace App\Http\Controllers;

use App\Plan;
use App\Subscribe;
use App\Invoice;
use App\RefInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\RefInvoice as RefResource;

class RefController extends Controller
{
    public function create_ref_invoice(Request $request) // user_id
    {
        $ref = new RefInvoice();
        if($request->user_id) {
            $ref->manager_id = $request->user_id;
        }
        $ref->user_id = $request->user_id;
        if($request->type_id) {
            $ref->type_id = $request->type_id;
        }
        $ref->save();
        return new RefResource($ref);
    }

    public function create_ref_invoice_detail(Request $request)
    {
        $invoice_type = $request->invoice_type;
        $ref = RefInvoice::findOrFail($request->ref_id);
        if($invoice_type == 1) {

        } elseif($invoice_type == 2) {

        }
    }

    public function ref($user_id)
    {
        $subscribe = Subscribe::where('user_id', $user_id)->first();
        $plan = Plan::findOrFail($subscribe->plan_id);
//        $last_invoice = Invoice::findOrFail($subscribe->last_invoice);

        // Ref = -(Iopperiod-Dns+Dos)*Iop/(Iopperiod)
        $iop_all = Invoice::findOrFail($subscribe->last_invoice)->ref_invoice->details->where('type', 'plan')->first();

        $d_start = $subscribe->start_at;
        if($iop_all->quantity > 1) {
            $d_end = $subscribe->start_at->addMonths($iop_all->quantity);
        } else {
            $d_end = $subscribe->start_at->addMonth();
        }
        $today = Carbon::today();
        $lost_days = $d_end->diff($today)->days; // количество не использованных дней
        $sub_day = $d_end->diff($d_start)->days + 1; // количество дней подписки
        $ref_sum = ($sub_day - $lost_days) / $iop_all->amount / $sub_day;
//        $sum_one_day = $iop_all->amount / $sub_day;
        return $ref_sum;
    }
}
