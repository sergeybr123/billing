<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribesType;
use App\Plan;
use App\RefInvoiceDetail;
use App\Service;
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
        return new RefResource(RefInvoice::findOrFail($ref->id));
    }

    public function create_ref_invoice_detail(Request $request)
    {
        $ref_detail_type = $request->ref_type; // 'plan', 'service', 'bot', 'mount_bonus', 'ref'
        $ref = RefInvoice::findOrFail($request->ref_id);
        $req_param = $request->param; // param - массив [] id, quantity
        $details = $ref->details;
        $typed = null;
        $paid_type = null;

        switch ($ref_detail_type) {
            case 'plan':
                $typed = Plan::faindOrFail($req_param->id)->first();
                $paid_type = 'App\\Plan';
                break;
            case 'service':
                $typed = Service::findOrFail($req_param->id)->first();
                $paid_type = 'App\\Service';
                break;
            case 'bot':
                $typed = AdditionalSubscribesType::findOrFail(1)->first();
                $paid_type = 'App\\AdditionalSubscribesType';
                break;
            case 'mount_bonus':
                $typed = AdditionalSubscribesType::findOrFail(2)->first();
                $paid_type = 'App\\AdditionalSubscribesType';
                break;
        }

        // Проверяем есть запись с таким типом в RefInvoice
        $ref_invoice_detail = null;
        foreach ($details as $detail)
        {
            if($detail->type == $ref_detail_type) {
                $ref_invoice_detail = $detail->id;
            }
        }

        if($ref_invoice_detail) {
            $ref_details = RefInvoiceDetail::findOrFail($ref_invoice_detail);
            $ref_details->paid_id = $typed->id;
            $ref_details->paid_type = $paid_type;
            $ref_details->price = $typed->price;
            $ref_details->quantity = $req_param->quantity;
            $ref_details->discount = $req_param->quantit < 12 ? 0 : ($typed->discount ?? 0);
            if($req_param->quantit >= 12) {
                $ref_details->amount = ($typed->price -($typed->price * ($typed->discount / 100))) * $req_param->quantity;
            } else {
                $ref_details->amount = $typed->price * $req_param->quantity;
            }
            $ref_details->save();
        } else {
            $new_ref_details = new RefInvoiceDetail();
            $new_ref_details->paid_id = $typed->id;
            $new_ref_details->paid_type = $paid_type;
            $new_ref_details->price = $typed->price;
            $new_ref_details->quantity = $req_param->quantity;
            $new_ref_details->discount = $req_param->quantit < 12 ? 0 : ($typed->discount ?? 0);
            if($req_param->quantit >= 12) {
                $new_ref_details->amount = ($typed->price -($typed->price * ($typed->discount / 100))) * $req_param->quantity;
            } else {
                $new_ref_details->amount = $typed->price * $req_param->quantity;
            }
            $new_ref_details->save();
        }

        $ref_inv_id = null;
        if(in_array($ref->type_id, [1, 2, 4])) {
            $ref_inv_id = $this->ref($ref->id);
        }
        if($ref_inv_id->error == 0) {
            $ref_invoice = new RefResource(RefInvoice::findOrFail($ref_inv_id->ref_id));
        }
        return $ref_invoice;
    }

    public function ref($ref_invoice_id)
    {
        $ref_invoice = RefInvoice::findOrFail($ref_invoice_id);
        $subscribe = Subscribe::where('user_id', $ref_invoice->user_id)->first();
        $plan_sub = Plan::findOrFail($subscribe->plan_id);
        $bot = AdditionalSubscribesType::where('id', 1)->first();
        $ref_invoice_plan = $ref_invoice->details->where('type', 'plan')->first(); // Получаем информацию о новой подписке
        if($ref_invoice_plan) {
            $plan_invoice = Plan::findOrFail($ref_invoice_plan->paid_id); // Получаем план новой подртскт
        }
        $add_bot = $ref_invoice->details->where('type', 'bot')->first(); // Получаем счет на покупку авточатов
        $last_plan = Invoice::findOrFail($subscribe->last_invoice)->ref_invoice->details->where('type', 'plan')->first(); // Получаем счет на предыдущую оплату тарифного плана
        if($last_plan) {
            $last_plan_invoice = Plan::findOrFail($last_plan->paid_id); // Получаем план Старой подписки
        }

        // Если подписка активна то счтаем сумму за оставшийся срок, иначе 0
        if($subscribe->active == true) {
            // высчитываем дни подписки
            $d_start = new Carbon($subscribe->start_at);
            $d_start->format('Y-m-d');
            if($last_plan->quantity >= 12) {
                $d_end = Carbon::parse($subscribe->start_at)->addMonths($last_plan->quantity);
            } else {
                $d_end = Carbon::parse($subscribe->start_at)->addMonth();
            }
            $d_end->format('Y-m-d');
            $today = Carbon::today();
            $count_day_before = $today->diff($d_start)->days; // количество дней от начала подписки
            $last_period_sub = $d_end->diff($d_start)->days; // количество дней подписки
            $lost_days = $last_period_sub - $count_day_before; // количество неиспользованных дней подписки
            $ref_sum = round(($lost_days * ($last_plan->amount / $last_period_sub)), 0);
        } else {
            $ref_sum =  0;
        }

        // Считаем стоимость авточатов
        $sum_bot = 0;
        $sum_bot_day = $bot->price / 30; // Стоимость авточата за 1 день
        if($subscribe->bot_count > $plan_sub->bot_count) {
            $sum_bot = (($sum_bot_day * $lost_days) * ($subscribe->bot_count - $last_plan_invoice->bot_count));
        }
        if($add_bot) {
            $sum_bot += (($bot->price * $lost_days) * $add_bot->quantity);
        }


        if($ref_invoice_plan) {
            $new_plan_amount = $ref_invoice_plan->amount;
        } else {
            $new_plan_amount = $last_plan->amount;
        }

        if($ref_sum > 0) {
            $ref = $ref_sum;
        } else {
            $ref = 0;
        }

        $Itot = ($new_plan_amount - $ref) + $sum_bot;

        $ref_id_detail = null;
        foreach ($ref_invoice->details as $detail) {
            if($detail->type == 'ref') {
                $ref_id_detail = $detail->id;
            }
        }


        // Считаем количество дней подписки
        if($ref_invoice_plan->quantity > 1) {
            $new_period_sub = Carbon::today()->addMonths(/*$ref_invoice_plan->quantity*/7);
        } else {
            $new_period_sub = Carbon::today()->addMonth();
        }
        $new_period_sub_days = Carbon::today()->diff($new_period_sub)->days - 1;

        // Работаем с 'ref' записью счета
        if($ref_id_detail) {
            $ref_detail = RefInvoiceDetail::findOrFail($ref_id_detail);
            $ref_detail->quantity = $lost_days + $new_period_sub_days;
            $ref_detail->amount = $Itot;
            $ref_detail->save();
        } else {
            $new_ref_details = new RefInvoiceDetail();
            $new_ref_details->ref_invoice_id = $ref_invoice->id;
            $new_ref_details->type = 'ref';
            $new_ref_details->quantity = $lost_days + $new_period_sub_days;
            $new_ref_details->amount = $Itot;
            $new_ref_details->save();
        }
        return response()->json(['error' => 0, 'ref_id' => $ref_invoice->id], 200);
    }
}
