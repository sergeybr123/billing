<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribesType;
use App\InvoiceOrder;
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
        $invoices = Invoice::where('id', '>', '101121')->get();
        foreach ($invoices as $invoice)
        {
            if($invoice->plan_id) {
                $plan = Plan::findOrFail($invoice->plan_id);
                $orders = new InvoiceOrder();
                $orders->invoice_id = $invoice->id;
                $orders->type = 'plan';
                $orders->model = 'App\\Invoice';
                $orders->paid_id = $plan->id;
                $orders->name = $plan->name;
                $orders->price = $plan->price;
                if ($invoice->period == null) {
                    $orders->quantity = 1;
                } else {
                    $orders->quantity = $invoice->period;
                }
                $orders->amount = $invoice->amount;
                $orders->save();
            }
            if ($invoice->service_id) {
                $service = Service::findOrFail($invoice->service_id);
                $orders = new InvoiceOrder();
                $orders->invoice_id = $invoice->id;
                $orders->type = 'service';
                $orders->model = 'App\\Service';
                $orders->paid_id = $service->id;
                $orders->name = $service->name;
                $orders->price = $service->price;
                $orders->quantity = 1;
                $orders->amount = $invoice->amount;
                $orders->save();
            }
        }
        return response()->json(['error' => 0, 'invoices' => $invoice]);
    }

    public function fiiBotCount()
    {
        $subscribes = Subscribe::all();
        foreach ($subscribes as $subscribe) {
            $subscribe->bot_count = $subscribe->plans->bot_count;
            $subscribe->save();
        }
    }


}
