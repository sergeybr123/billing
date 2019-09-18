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
        $ref->type_id = $request->type_id;
        $ref->save();
        return new RefResource(RefInvoice::findOrFail($ref->id));
    }

    public function create_ref_invoice_detail(Request $request)
    {
        $ref_detail_type = $request->ref_type; // 'plan', 'service', 'bot', 'bonus', 'ref'
        $ref = RefInvoice::findOrFail($request->ref_id);
        $req_param = $request->param; // param - массив [] id, quantity
        $details = $ref->details;
        $typed = null; // модель
        $paid_type = null;

        // Выбираем модель данных
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
                $typed = Service::findOrFail($req_param->id)->first();
                $paid_type = 'App\\Service';
                break;
            case 'bonus':
                $typed = Service::findOrFail($req_param->id)->first();
                $paid_type = 'App\\Service';
                break;
        }

        // Проверяем есть запись с таким типом в RefInvoice
        $ref_invoice_detail = null;
        foreach ($details as $detail) {
            if($detail->id == $req_param->detail_id) {
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
            $new_ref_details->discount = $typed->discount ?? 0;
            if($typed->discount) {
                if($req_param->quantit >= 12) {
                    $new_ref_details->amount = ($typed->price -($typed->price * ($typed->discount / 100))) * $req_param->quantity;
                } else {
                    $new_ref_details->amount = $typed->price * $req_param->quantity;
                }
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

    public function ref($ref_invoice_id) //invoice_id=101563
    {
        $ref_invoice = RefInvoice::findOrFail($ref_invoice_id);
        $subscribe = Subscribe::where('user_id', $ref_invoice->user_id)->first();
        $plan_sub = Plan::findOrFail($subscribe->plan_id);
        $ref_invoice_plan = $ref_invoice->details->where('type', 'plan')->first(); // Получаем информацию о новой подписке
        if($ref_invoice_plan) {
            $plan_invoice = Plan::findOrFail($ref_invoice_plan->paid_id); // Получаем план новой подртскт
        }
        $add_bot = $ref_invoice->details->where('type', 'bot')->first(); // Получаем счет на покупку авточатов
        $bot = Service::where('type', 'bot')->get();
        $ref = 0;
        $ref_bot =0;
        $bot_count = 0; // записываем количество авточатов на новый период
        $last_bot_plan = 0;

        // Если подписка пакета платная
        if($plan_sub->price > 0.00 && $subscribe->active == true) {
            $last_plan = Invoice::findOrFail($subscribe->last_invoice)->ref_invoice->details->where('type', 'plan')->first(); // Получаем счет на предыдущую оплату тарифного плана
            $last_bot = Invoice::findOrFail($subscribe->last_invoice)->ref_invoice->details->where('type', 'bot')->first(); // Получаем счет на предыдущую оплату тарифного плана
            //выбираем количество автчатов свыше подписки
            if($subscribe->bot_count > $plan_sub->bot_count) {
                $bot_count += ($subscribe->bot_count - $plan_sub->bot_count);
                $last_bot_plan += $plan_sub->bot_count;
            }
            // Если подписка активна то счтаем сумму за оставшийся срок, иначе 0
            // высчитываем дни подписки
            $d_start = new Carbon($subscribe->start_at);
            $d_end = new Carbon($subscribe->start_at);
            $d_end->addMonths($last_plan->quantity);
            $today = Carbon::today();
            $count_day_before = $today->diff($d_start)->days; // количество дней от начала подписки
            $last_period_sub = $d_end->diff($d_start)->days; // количество дней подписки
            $lost_days = $last_period_sub - $count_day_before - 1; // количество неиспользованных дней подписки
            // Считаем сумму оставшейся подписки
            $ref_sum = round(($lost_days * ($last_plan->amount / $last_period_sub)), 0);
            // Считаем сумму авточатов
            if($bot_count > 0) {
                $ref_sum_bot = round(($lost_days * ($last_bot->amount / $last_period_sub)), 0);
            }
            // Проверяем, чтобы сумма ref всегда была >= 0
            if($ref_sum > 0) {
                $ref = $ref_sum;
            }
            if($ref_sum_bot > 0) {
                $ref_bot = $ref_sum_bot;
            }
        }

        // Считаем количество дней новой подписки
        $new_period_sub = Carbon::today()->addMonths($ref_invoice_plan->quantity);
        $new_period_sub_days = Carbon::today()->diff($new_period_sub)->days - 1;

        if($last_bot_plan != $plan_invoice->bot_couny)
        $bc = ($bot_count + $add_bot->quantity);
        $sum_bot = round((($bot->price * $new_period_sub_days) * $bc), 0);

        // Считаем итоговую сумму пользователя
        $Itot = ($ref_invoice_plan->amount + $sum_bot) - $ref - $ref_bot;

        // записываем ref в базу данных
        $ref_id_detail = null;
        foreach ($ref_invoice->details as $detail) {
            if($detail->type == 'ref') {
                $ref_id_detail = $detail->id;
            }
        }
        // Работаем с 'ref' записью счета
        if($ref_id_detail) {
            $ref_detail = RefInvoiceDetail::findOrFail($ref_id_detail);
            $ref_detail->details = ['ref' => $ref, 'ref_bot' => $ref_bot];
            $ref_detail->quantity = $new_period_sub_days;
            $ref_detail->amount = $Itot;
            $ref_detail->save();
        } else {
            $new_ref_details = new RefInvoiceDetail();
            $new_ref_details->ref_invoice_id = $ref_invoice->id;
            $new_ref_details->type = 'ref';
            $new_ref_details->details = ['ref' => $ref, 'ref_bot' => $ref_bot];
            $new_ref_details->amount = $ref;
            $new_ref_details->quantity = $new_period_sub_days;
            $new_ref_details->amount = $Itot;
            $new_ref_details->save();
        }

        try {
            return response()->json(['error' => 0, 'ref_id' => $ref_invoice_id]);
        } catch (\Exception $e) {
            return response()->json(['error' => 1]);
        }
    }

    public function createInvoice(Request $request)
    {
        $ref_id = $request->ref_id;
        $ref_invoice = RefInvoice::findOrFail($ref_id);
        $ref_invoice_details = $ref_invoice->details;
        $invoice_ref = null;
        $total_amount = 0;

        foreach ($ref_invoice_details as $detail) {
            if($detail->type == 'ref') {
                $invoice_ref = $detail;
            }
            if(in_array($detail->type, ['service', 'bonus', 'ref'])) {
                $total_amount += $detail->amount;
            }
        }

        $invoice = new Invoice();
        $invoice->manager_id = $invoice_ref->manager_id;
        $invoice->user_id = $invoice_ref->user_id;
        $invoice->amount = $total_amount;
        $invoice->type_id = $invoice_ref->type_id;
        $invoice->description = $invoice_ref->description;
        $invoice->save();

        $ref_invoice->invoice_id = $invoice->id;
        $ref_invoice->save();
    }
}
