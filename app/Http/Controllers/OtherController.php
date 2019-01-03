<?php

namespace App\Http\Controllers;

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
}
