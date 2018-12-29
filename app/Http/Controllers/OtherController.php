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
    }

    public function changePrice()
    {
        $start = Plan::findOrFail(4);
        $start->price = 2500;
        $start->save();
        $business = Plan::findOrFail(5);
        $business->price = 3500;
        $business->save();
        $enterprise = Plan::findOrFail(6);
        $enterprise->price = 6000;
        $enterprise->save();
    }
}
