<?php

namespace App\Http\Controllers;

use App\Models\BillingConfig;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingConfiguration extends Controller
{
    public function index(Request $request)
    {
        $billconfig = BillingConfig::first();
        return Inertia::render('Setup/BillConfig', ['billconfig' => $billconfig]);

    }

    public function store(Request $request)
    {
      
        $bill_config = new BillingConfig();
        if(!empty($request->exclude_list)){
            $bill_config->exclude_list = '';
            foreach ($request->exclude_list as $key => $value) {
                if($key !== array_key_last($request->exclude_list))
                $bill_config->exclude_list .= $value['id'].',';
                else
                $bill_config->exclude_list .= $value['id'];
            }
            
        }

        $bill_config->mrc_day = $request->mrc_day;
        $bill_config->prepaid_day = $request->prepaid_day;
        $bill_config->mrc_month = $request->mrc_month;
        $bill_config->prepaid_month = $request->prepaid_month;
        $bill_config->save();
         return redirect()->back()->with('message', 'Config Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        if ($request->has('id')) {
            $bill_config =  BillingConfig::find($request->input('id'));
            if(!empty($request->exclude_list)){
                $bill_config->exclude_list = '';
                foreach ($request->exclude_list as $key => $value) {
                    if($key !== array_key_last($request->exclude_list))
                    $bill_config->exclude_list .= $value['id'].',';
                    else
                    $bill_config->exclude_list .= $value['id'];
                }
                
            }
            $bill_config->mrc_day = $request->mrc_day;
            $bill_config->prepaid_day = $request->prepaid_day;
            $bill_config->mrc_month = $request->mrc_month;
            $bill_config->prepaid_month = $request->prepaid_month;
            $bill_config->update();
            return redirect()->back()
                    ->with('message', 'Config Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingConfig::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
