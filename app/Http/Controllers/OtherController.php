<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribesType;
//use App\Http\Resources\PlanFeature;
use App\InvoiceOrder;
use App\PlansFeature;
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

    public function create_ref_inv()
    {
        $subscribes = Subscribe::whereIn('plan_id', [4, 5, 6, 7])->where('active', true)/*->take(1)->with('invoices')*/->get();
        foreach ($subscribes as $subscribe) {
            $inv = $subscribe->invoices[0];
//            $subscr_inv = Subscribe::findOrFail($subscribe->id)->invoices->where('paid', 1)->where('plan_id', '!=', null);
//            $present_mount=0;
//            $inv = $subscribe->invoices;
//            $subscribe->last_invoice = $inv[0]->id;
//            $subscribe->save();
//
//            $inv_index = 0;
//
//            //Считаем сроки
//            $today = Carbon::today();
//            $d_end =  new Carbon($subscribe->end_at);
//            $d_end_sub =  new Carbon($subscribe->end_at);
//            $d_start = new Carbon($subscribe->start_at);
//            $sub_days = $d_end->diff($d_start)->days;
//            $last_days = $d_end->diff($today)->days;
//            $first_days = $d_start->diff($today)->days;
//            return $inv[0];
            //Создаем реф
            if(!is_null($inv)) {
                $ref = new RefInvoice();
                $ref->invoice_id = $inv->id;
                $ref->manager_id = $inv->manager_id ?? null;
                $ref->user_id = $inv->user_id;
                $ref->amount = $inv->amount;
                $ref->type_id = $inv->type_id;
                $ref->description = $inv->description;
                $ref->save();

                $ref_details = new RefInvoiceDetail();
                $ref_details->ref_invoice_id = $ref->id;
                $ref_details->type = 'plan';
                $ref_details->save();
            }

////            foreach ($subscr_inv as $item) {
////
////            }
////            return $d_start; // count($inv);
//            for($i=0;$i<=count($inv);$i++) {
////                $inv_item = $inv[$i];
//                if($inv[$i]->price == 0) {
//                    $present_mount += 1;
//                }
//                $inv_today_1 = Carbon::today();
//                $inv_today_2 = Carbon::today();
//                $inv_per_end = $inv_today_1->addMonths($inv[$i]->period);
//                $inv_days = $inv_today_2->diff($inv_per_end)->days;
//                return $inv_days;
//                if($inv) {
//
//                }
//            }





        }
//        return $inv;
    }

    public function fillInvoiceOrders()
    {
        $subscribes = Subscribe::whereIn('plan_id', [4, 5, 6, 7])->where('active', true)->take(1)/*->with('invoices')*/->get();
        $sub_arr = [];
        foreach ($subscribes as $subscribe) {

            $subscr_inv = Subscribe::findOrFail($subscribe->id)->invoices->where('paid', 1)->where('plan_id', '!=', null);
//            return $subscr_item;
            $today = Carbon::today();
            $d_end =  new Carbon($subscribe->end_at);
            $d_start = new Carbon($subscribe->start_at);
            $sub_days = $d_end->diff($d_start)->days;
            $last_days = $d_end->diff($today)->days;
            $first_days = $d_start->diff($today)->days;

            if($last_days < 30) {

            }

//            if($last_days <= 90) {
//                array_push($sub_arr, $subscribe);
//            }

            $invoices = $subscribe->invoices/*->orderBy('id', 'desc')->get()*/;
//            return response()->json([$invoices]);
            $days_arr = [];
            $i_count = 0;
            $i_count_paid = 0;
            $i_count_price = 0;
            foreach($invoices as $invoice) {
                $i_count++;
                if($invoice->paid == true) {
                    $i_count_paid++;
                    if ($invoice->price > 0.00) {
                        $i_count_price++;
                        $i_per_start = Carbon::today();
                        $i_per_end = Carbon::today()->addMonths($invoice->period);
                        $per_days = $i_per_start->diff($i_per_end)->days;
                        array_push($days_arr, $per_days);
                    } else {

                    }
                }
            }
        }
        return response()->json([$i_count, $i_count_paid, $i_count_price, 'count' => count($invoices), 'sub_days' => $sub_days, 'first_days' => $first_days, 'last_days' => $last_days, 'd_start' => $d_start->toDateTimeString(), 'd_end' => $d_end->toDateTimeString(), 'invoices' => $invoices]);


        /*---Переписать метод---*/
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
            $d_start->subMonths($period);            $sub_days = $d_end->diff($d_start)->days;
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
        $subscribes = Subscribe::get();
        $count_bot = 0;
        foreach ($subscribes as $subscribe) {
            $count_bot++;
            $subscribe->bot_count = $subscribe->plan->bot_count;
            $subscribe->save();
        }
        return $count_bot;
    }

    public function create_trial($plan_id)
    {
        $plan_t = Plan::findOrFail($plan_id);
        if(!$plan_t) {
            $plan = new Plan();
            $plan->code = 'trial';
            $plan->name = 'Trial';
            $plan->discount = 0;
            $plan->price = 0.00;
            $plan->interval = 'days';
            $plan->sort_order = 0;
            $plan->on_show = 0;
            $plan->active = true;
            $plan->bot_count = 1;
            $plan->on_show = 0;
            $plan->on_show = 0;
            $plan->save();
        }
        return $plan;
    }

    public function rename_test_trial()
    {
        $plan = Plan::where(['code' => 'test'])->first();
        if($plan) {
            $plan->code = 'trial';
            $plan->name = 'Trial';
            $plan->period = 30;
            $plan->bot_count = 5;
            $plan->save();

            return response()->json(['error' => 0, 'message' => 'Completed']);
        } else {
            return response()->json(['error' => 404, 'message' => 'Not found']);
        }
    }

    public function create_feature_trial()
    {
        $ent = Plan::where('code', 'enterprise')->first();
        $trial  = Plan::where('code', 'trial')->first();
        if($ent) {
            $feateres = PlansFeature::where('plan_id', $ent->id)->get();
        }
        if($trial) {
            if($feateres) {
                foreach ($feateres as $item) {
                    $pf = new PlansFeature();
                    $pf->plan_id = $trial->id;
                    $pf->feature_id = $item->feature_id;
                    $pf->save();
                }
            }
        }
    }

    public function create_ref_for_paid()
    {

    }




}
