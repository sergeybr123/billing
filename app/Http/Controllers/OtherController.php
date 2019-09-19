<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribesType;
use App\InvoiceOrder;
use App\RefInvoice;
use App\RefInvoiceDetail;
use App\Service;
use App\Subscribe;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Plan;
use App\Invoice;

class OtherController extends Controller
{
    public function setNotActive()
    {
        $invoices = Invoice::where('paid', 0)->get();
        foreach ($invoices as $invoice) {
            $invoice->status = 'completed';
            $invoice->save();
        }
        return response()->json(['error' => 0]);
    }

    public function changePrice()
    {
        $month = Plan::findOrFail(2);
        $month->active = false;
        $month->save();
        $year = Plan::findOrFail(3);
        $year->active = false;
        $year->save();
        $start = Plan::findOrFail(4);
        $start->discount = 20;
        $start->price = 2500;
        $start->save();
        $business = Plan::findOrFail(5);
        $business->discount = 25;
        $business->price = 3500;
        $business->save();
        $enterprise = Plan::findOrFail(6);
        $enterprise->discount = 30;
        $enterprise->price = 6000;
        $enterprise->save();
        return response()->json(['error' => 0]);
    }

    public function fillInvoiceOrders()
    {
        $subscribes = Subscribe::whereIn('plan_id', [4, 5, 6])->where('active', true)->get();
        $count = 0;
        foreach($subscribes as $subscribe) {
            $month = 0;
            $invoices = Invoice::whereNotNull('plan_id')->where('amount', '>', 0)->orderBy('created_at', 'desc')->take(2)->get();
            if($invoices[0]->amount == 0) {
                $month += 1;
                $invoice = $invoices[1];
            } else {
                $invoice = $invoices[0];
            }
            $ref = new RefInvoice();
            $ref->invoice_id = $invoice->id;
            $ref->manager_id = $invoice->manager_id ?? null;
            $ref->user_id = $invoice->user_id;
            $ref->amount = $invoice->amount;
            $ref->type_id = $invoice->type_id;
            $ref->description = $invoice->description;
            $ref->save();
            $ref_details = new RefInvoiceDetail();
            $ref_details->ref_invoice_id = $ref->id;
            $ref_details->type = 'plan';
            $ref_details->paid_id = $invoice->plan_id;
            $ref_details->paid_type = 'App\\Plan';
            $ref_details->price = $invoice->plan->price;
            // Высчитываем период подписки из последнего счета
            $d_end =  new Carbon($subscribe->end_at);
            $sub_start = new Carbon($subscribe->start_at);
            $d_start = new Carbon($subscribe->end_at);

            $real_sub_days = $d_end->diff($sub_start)->days;
            if($subscribe->interval == 'year' && $invoice->period < 12) {
                $period = 12 + $month;
            } else {
                $period = $invoice->period + $month;
            }
            $d_start->subMonths($period);
            $sub_days = $d_end->diff($d_start)->days;
//            return $invoice->user_id;
            $ref_details->quantity = $sub_days;
            $ref_details->discount = $invoice->period >= 12 ? $invoice->plan->discount : 0;
            $ref_details->amount = $invoice->amount;
            $ref_details->save();
            // проставляем дату начала подписки и последний нивойс
            $subscribe->start_at = $d_start;
            $subscribe->interval = $invoice->period >= 12 ? 'year' : 'month';
            $subscribe->last_invoice = $invoice->id;
            $subscribe->save();
            $count++;
        }
        return $count;

//        $invoices = Invoice::where('id', '>', '101121')->get();
//        foreach ($invoices as $invoice)
//        {
//            if($invoice->plan_id) {
//                $plan = Plan::findOrFail($invoice->plan_id);
//                $orders = new InvoiceOrder();
//                $orders->invoice_id = $invoice->id;
//                $orders->type = 'plan';
//                $orders->model = 'App\\Invoice';
//                $orders->paid_id = $plan->id;
//                $orders->name = $plan->name;
//                $orders->price = $plan->price;
//                if ($invoice->period == null) {
//                    $orders->quantity = 1;
//                } else {
//                    $orders->quantity = $invoice->period;
//                }
//                $orders->amount = $invoice->amount;
//                $orders->save();
//            }
//            if ($invoice->service_id) {
//                $service = Service::findOrFail($invoice->service_id);
//                $orders = new InvoiceOrder();
//                $orders->invoice_id = $invoice->id;
//                $orders->type = 'service';
//                $orders->model = 'App\\Service';
//                $orders->paid_id = $service->id;
//                $orders->name = $service->name;
//                $orders->price = $service->price;
//                $orders->quantity = 1;
//                $orders->amount = $invoice->amount;
//                $orders->save();
//            }
//        }
//        return response()->json(['error' => 0, 'invoices' => $invoice]);
    }

    public function fillBotCount()
    {
        $subscribes = Subscribe::all();
        foreach ($subscribes as $subscribe) {
            $subscribe->bot_count = $subscribe->plans->bot_count;
            $subscribe->save();
        }
    }


}
