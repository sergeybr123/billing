<?php

namespace App\Http\Controllers;

use App\AdditionalSubscribe;
use App\AdditionalSubscribesType;
use App\CPLog;
use App\Feature;
use App\Invoice;
use App\Plan;
use App\PlansFeature;
use App\Service;
use App\Subscribe;
use App\TypeInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DbController extends Controller
{
    public function insert_tables()
    {
        $plans = DB::connection('old')->table('plans')->get();
        DB::table('plans')->delete();
        DB::statement('ALTER TABLE plans AUTO_INCREMENT = 1;');
        foreach ($plans as $plan) {
            $n_plan = new Plan();
            $n_plan->id = $plan->id;
            $n_plan->code = $plan->code;
            $n_plan->name = $plan->name;
            $n_plan->discount = $plan->discount;
//            $n_plan->discount_option = $plan->discount_option;
            $n_plan->description = $plan->description;
            $n_plan->price = $plan->price;
            $n_plan->interval = $plan->interval;
            $n_plan->trial_period_days = $plan->trial_period_days;
            $n_plan->sort_order = $plan->sort_order;
            $n_plan->on_show = $plan->on_show;
            $n_plan->active = $plan->active;
            $n_plan->bot_count = $plan->bot_count;
            $n_plan->save();
        }
        $features = DB::connection('old')->table('features')->get();
        DB::table('features')->delete();
        DB::statement('ALTER TABLE features AUTO_INCREMENT = 1;');
        foreach ($features as $feature) {
            $n_feat = new Feature();
            $n_feat->id = $feature->id;
            $n_feat->is_group = $feature->is_group;
            $n_feat->parent_id = $feature->parent_id;
            $n_feat->code = $feature->code;
            $n_feat->route = $feature->route;
            $n_feat->name = $feature->name;
            $n_feat->description = $feature->description;
            $n_feat->interval = $feature->interval;
            $n_feat->interval_count = $feature->interval_count;
            $n_feat->sort_order = $feature->sort_order;
            $n_feat->active = $feature->active;
            $n_feat->save();
        }
        $plans_features = DB::connection('old')->table('plans_features')->get();
        DB::table('plans_features')->delete();
        DB::statement('ALTER TABLE plans_features AUTO_INCREMENT = 1;');
        foreach ($plans_features as $pf) {
            $n_pf = new PlansFeature();
            $n_pf->id = $pf->id;
            $n_pf->plan_id = $pf->plan_id;
            $n_pf->feature_id = $pf->feature_id;
            $n_pf->save();
        }
        $type_invoices = DB::connection('old')->table('type_invoices')->get();
        DB::table('type_invoices')->delete();
        DB::statement('ALTER TABLE type_invoices AUTO_INCREMENT = 1;');
        foreach ($type_invoices as $ti) {
            $n_ti = new TypeInvoice();
            $n_ti->id = $ti->id;
            $n_ti->name = $ti->name;
            $n_ti->save();
        }
        $additional_subscribes = DB::connection('old')->table('additional_subscribes')->get();
        DB::table('additional_subscribes')->delete();
        DB::statement('ALTER TABLE additional_subscribes AUTO_INCREMENT = 1;');
        foreach ($additional_subscribes as $as) {
            $n_as = new AdditionalSubscribe();
            $n_as->id = $as->id;
            $n_as->subscribe_id = $as->subscribe_id;
            $n_as->additional_subscribe_type_id = $as->additional_subscribe_type_id;
            $n_as->quantity = $as->quantity;
            $n_as->price = $as->price;
            $n_as->trial_ends_at = $as->trial_ends_at;
            $n_as->start_at = $as->start_at;
            $n_as->end_at = $as->end_at;
            $n_as->save();
        }
        $additional_subscribes_types = DB::connection('old')->table('additional_subscribes_types')->get();
        DB::table('additional_subscribes_types')->delete();
        DB::statement('ALTER TABLE additional_subscribes_types AUTO_INCREMENT = 1;');
        foreach ($additional_subscribes_types as $ast) {
            $n_ast = new AdditionalSubscribesType();
            $n_ast->id = $ast->id;
            $n_ast->name = $ast->name;
            $n_ast->price = $ast->price;
            $n_ast->save();
        }
        $services = DB::connection('old')->table('services')->get();
        DB::table('services')->delete();
        DB::statement('ALTER TABLE services AUTO_INCREMENT = 1;');
        foreach ($services as $s) {
            $n_s = new Service();
            $n_s->id = $s->id;
            $n_s->plan_id = $s->plan_id;
            $n_s->type = '';
            $n_s->name = $s->name;
            $n_s->description = $s->description;
//            $n_s->discount = $s->discount;
//            $n_s->discount_option = $s->discount_option;
//            $n_s->quantity = $s->quantity;
            $n_s->price = $s->price;
            $n_s->active = $s->active;
            $n_s->save();
        }
        $subscribes = DB::connection('old')->table('subscribes')->get();
        DB::table('subscribes')->delete();
        DB::statement('ALTER TABLE subscribes AUTO_INCREMENT = 1;');
        foreach ($subscribes as $sb) {
            $n_sb = new Subscribe();
            $n_sb->id = $sb->id;
            $n_sb->user_id = $sb->user_id;
            $n_sb->plan_id = $sb->plan_id;
            $n_sb->interval = $sb->interval;
//            $n_sb->bot_count = $sb->bot_count;
            $n_sb->trial_ends_at = $sb->trial_ends_at;
            $n_sb->start_at = $sb->start_at;
            $n_sb->end_at = $sb->end_at;
            $n_sb->active = $sb->active;
            $n_sb->last_invoice = $sb->last_invoice;
            $n_sb->save();
        }
        $invoices = DB::connection('old')->table('invoices')->get();
        DB::table('invoices')->delete();
        DB::statement('ALTER TABLE invoices AUTO_INCREMENT = 1;');
        foreach ($invoices as $in) {
            $n_in = new Invoice();
            $n_in->id = $in->id;
            $n_in->manager_id = $in->manager_id;
            $n_in->user_id = $in->user_id;
            $n_in->amount = $in->amount;
            $n_in->type_id = $in->type_id;
            $n_in->plan_id = $in->plan_id;
            $n_in->period = $in->period;
            $n_in->service_id = $in->service_id;
            $n_in->description = $in->description;
            $n_in->paid = $in->paid;
            $n_in->paid_at = $in->paid_at;
            $n_in->options = $in->options;
            $n_in->usages = $in->usages;
            $n_in->status = $in->status;
            $n_in->save();
        }
        $c_p_logs = DB::connection('old')->table('c_p_logs')->get();
        DB::table('c_p_logs')->delete();
        DB::statement('ALTER TABLE c_p_logs AUTO_INCREMENT = 1;');
        foreach ($c_p_logs as $cpl) {
            $n_cpl = new CPLog();
            $n_cpl->id = $cpl->id;
            $n_cpl->invoice_id = $cpl->invoice_id;
            $n_cpl->transaction_id = $cpl->transaction_id;
            $n_cpl->currency = $cpl->currency;
            $n_cpl->cardFirstSix = $cpl->cardFirstSix;
            $n_cpl->cardLastFour = $cpl->cardLastFour;
            $n_cpl->cardType = $cpl->cardType;
            $n_cpl->name = $cpl->name;
            $n_cpl->email = $cpl->email;
            $n_cpl->issuer = $cpl->issuer;
            $n_cpl->token = $cpl->token;
            $n_cpl->save();
        }
        try {
            return response()->json(['error' => 0, 'message' => '']);
        } catch (\Exception $e) {
            return response()->json(['error' => 1, 'message' => $e]);
        }

    }

    public function update_invoice_created()
    {
        /*$plans = DB::connection('old')->table('plans')->get();
        foreach ($plans as $plan) {
            $item = Plan::findOrFail($plan->id);
            $item->created_at = $plan->created_at;
            $item->save();
        }
        $features = DB::connection('old')->table('features')->get();
        foreach ($features as $feature) {
            $item = Feature::findOrFail($feature->id);
            $item->created_at = $feature->created_at;
            $item->save();
        }
        $plans_features = DB::connection('old')->table('plans_features')->get();
        foreach ($plans_features as $pf) {
            $item = PlansFeature::findOrFail($pf->id);
            $item->created_at = $pf->created_at;
            $item->save();
        }
        $type_invoices = DB::connection('old')->table('type_invoices')->get();
        foreach ($type_invoices as $ti) {
            $item = TypeInvoice::findOrFail($ti->id);
            $item->created_at = $ti->created_at;
            $item->save();
        }
        $additional_subscribes = DB::connection('old')->table('additional_subscribes')->get();
        foreach ($additional_subscribes as $as) {
            $item = AdditionalSubscribe::findOrFail($as->id);
            $item->created_at = $as->created_at;
            $item->save();
        }
        $additional_subscribes_types = DB::connection('old')->table('additional_subscribes_types')->get();
        foreach ($additional_subscribes_types as $ast) {
            $item = AdditionalSubscribesType::findOrFail($ast->id);
            $item->created_at = $ast->created_at;
            $item->save();
        }
        $services = DB::connection('old')->table('services')->get();
        foreach ($services as $s) {
            $item = Service::findOrFail($s->id);
            $item->created_at = $s->created_at;
            $item->save();
        }
        $subscribes = DB::connection('old')->table('subscribes')->get();
        foreach ($subscribes as $sb) {
            $item = Subscribe::findOrFail($sb->id);
            $item->created_at = $sb->created_at;
            $item->save();
        }*/
        $invoices = DB::connection('old')->table('invoices')->get();
        foreach ($invoices as $in) {
            $n_inv = Invoice::findOrFail($in->id);
            $n_inv->created_at = $in->created_at;
            $n_inv->save();
        }
        $c_p_logs = DB::connection('old')->table('c_p_logs')->get();
        foreach ($c_p_logs as $cpl) {
            $n_inv = CPLog::findOrFail($cpl->id);
            $n_inv->created_at = $cpl->created_at;
            $n_inv->save();
        }
    }

    public function insert_service()
    {
        DB::table('services')->delete();
        DB::statement('ALTER TABLE plans AUTO_INCREMENT = 1;');
        DB::table('services')->insert([
            'type' => 'service',
            'name' => 'Разработка авточата',
            'discount' => 0,
            'price' => 15000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('services')->insert([
            'type' => 'bot',
            'name' => 'Дополнительный авточата',
            'discount' => 0,
//            'discount_option' => ['type' => 'plan', 'value' => 'discount'],
            'price' => 500,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('services')->insert([
            'type' => 'bonus',
            'name' => 'Бесплатно месяц',
            'discount' => 0,
            'price' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
