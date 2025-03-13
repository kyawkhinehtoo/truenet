<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublicIpAddress;
use App\Models\IPUsageHistory;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class PublicIpController extends Controller
{
    //
    public function getCustomerIp($customer_id){
        if($customer_id){

            $public_ips = PublicIpAddress::join('ip_usage_history','ip_usage_history.ip_id','=','public_ip_addresses.id')
            ->where('public_ip_addresses.customer_id','=',$customer_id)
            ->where('ip_usage_history.customer_id','=',$customer_id)
            ->select('public_ip_addresses.*','ip_usage_history.id as ip_usage_history_id','ip_usage_history.start_date','ip_usage_history.end_date')
            ->get();
            return response()->json($public_ips,200);
        }
       
    }
    public function store(Request $request){
        Validator::make($request->all(), [
            'ip_address' => 'required|unique:public_ip_addresses,ip_address', 
            'description' => 'required|max:255', 
            'annual_charge' => 'required|numeric', // Ensure it is a numeric value
            'currency' => 'required|max:255', 
            'customer_id' => 'required|exists:customers,id', // Ensure it exists in the customers table
            'start_date' => 'required|date|date_format:Y-m-d', // Ensure it is a valid date with the format 'Y-m-d'
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date', // Ensure it is a valid date after or equal to start_date
        ])->validate();
        if ($request) {
            $public_ip = new PublicIpAddress();
            $public_ip->ip_address = $request->ip_address;
            $public_ip->description = $request->description;
            $public_ip->annual_charge = $request->annual_charge;
            $public_ip->currency = $request->currency;
            $public_ip->customer_id = $request->customer_id;
            $public_ip->save();
            if($public_ip->id){
                $ip_usage_history = new IPUsageHistory();
                $ip_usage_history->start_date = $request->start_date;
                $ip_usage_history->end_date = $request->end_date;
                $ip_usage_history->ip_id = $public_ip->id;
                $ip_usage_history->customer_id = $request->customer_id;
                $ip_usage_history->save();
            }
            return redirect()->back()->with('message', 'IP added Successfully.');
        }

    }
    public function update(Request $request){
        Validator::make($request->all(), [
            'id' => 'required', 
            'ip_usage_history_id' => 'required', 
            'ip_address' => [
                'required',
                Rule::unique('public_ip_addresses')->ignore($request->id),
            ],
            'description' => 'required|max:255', 
            'annual_charge' => 'required|numeric',
            'currency' => 'required|max:255', 
            'customer_id' => 'required',
            'start_date' => 'required|date', 
            'end_date' => 'required|date', 
        ])->validate();
        if ($request->id) {
            $public_ip =  PublicIpAddress::find($request->id);
            $public_ip->ip_address = $request->ip_address;
            $public_ip->description = $request->description;
            $public_ip->annual_charge = $request->annual_charge;
            $public_ip->currency = $request->currency;
            $public_ip->customer_id = $request->customer_id;
            $public_ip->update();
            if($request->ip_usage_history_id){
                $ip_usage_history = IPUsageHistory::find($request->ip_usage_history_id);
                $ip_usage_history->start_date = $request->start_date;
                $ip_usage_history->end_date = $request->end_date;
                $ip_usage_history->ip_id = $public_ip->id;
                $ip_usage_history->customer_id = $request->customer_id;
                $ip_usage_history->update();
            }
            return redirect()->back()->with('message', 'IP updated Successfully.');
        }

    }

    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            PublicIpAddress::find($request->input('id'))->delete();
            
            // $ip_usage_history = IPUsageHistory::find($request->ip_usage_history_id);
            // if ($ip_usage_history) {
            // $ip_usage_history->end_date= now();
            // $ip_usage_history->update();
            // }
            return redirect()->back();
        }
    }
}
