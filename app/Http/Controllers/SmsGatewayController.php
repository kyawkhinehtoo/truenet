<?php

namespace App\Http\Controllers;

use App\Models\SmsGateway;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

class SmsGatewayController extends Controller
{
    public function index(Request $request)
    {
         $gateway = SmsGateway::first();
        return Inertia::render('Setup/SmsGateway', ['gateway' => $gateway]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'sender_id' => ['required'],
            'status' => ['required'],
            'api_url' => ['required'],
            'single_sms' => ['required'],
            'info' => ['required'],
            'sid' => ['required'],
            'token' => ['required'],
        ])->validate();

        $smsgateway = new SmsGateway();
        $smsgateway->sender_id = $request->sender_id;
        $smsgateway->status = $request->status;
        $smsgateway->api_url = $request->api_url;
        $smsgateway->sid = $request->sid;
        $smsgateway->token = $request->token;
        $smsgateway->single_sms = $request->single_sms;
        $smsgateway->info = $request->info;
        $smsgateway->bulk_sms = $request->bulk_sms;
        $smsgateway->save();
         return redirect()->back()->with('message', 'API Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'sender_id' => ['required'],
            'status' => ['required'],
            'api_url' => ['required'],
            'sid' => ['required'],
            'token' => ['required'],
            'single_sms' => ['required'],
            'info' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $smsgateway =  SmsGateway::find($request->input('id'));
            $smsgateway->sender_id = $request->sender_id;
            $smsgateway->status = $request->status;
            $smsgateway->api_url = $request->api_url;
            $smsgateway->sid = $request->sid;
            $smsgateway->token = $request->token;
            $smsgateway->single_sms = $request->single_sms;
            $smsgateway->info = $request->info;
            $smsgateway->bulk_sms = $request->bulk_sms;
            $smsgateway->update();
            return redirect()->back()
                    ->with('message', 'Data Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            SmsGateway::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}