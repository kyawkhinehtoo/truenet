<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bills;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DailyReceiptController extends Controller
{
    public function index(Request $request)
    {   
        $bill_list = Bills::all();
        $today_collection = ReceiptRecord::whereDate('receipt_records.created_at',Carbon::today())
                                            ->sum('receipt_records.collected_amount');
        $yesterday_collection = ReceiptRecord::whereDate('receipt_records.created_at',Carbon::yesterday())
                                            ->sum('receipt_records.collected_amount');
    
        $select_total = ReceiptRecord::join('customers','customers.id','=','receipt_records.customer_id')
                                ->join('invoices','invoices.id','=','receipt_records.invoice_id')
                                ->join('bills','bills.id','=','receipt_records.bill_id')
                                ->when($request->general, function ($query, $general) {
                                    $query->where(function ($query) use ($general) {
                                        $query->where('customers.name','LIKE', '%'.$general.'%')
                                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                                        ->orWhere('invoices.invoice_number', 'LIKE', '%' . $general . '%')
                                        ->orWhere('receipt_records.receipt_number', 'LIKE', '%' . $general . '%');
                                            });
                                    })
                                ->when($request->bill_id, function ($query, $bills) {
                                    $b_list = array();
                                    foreach ($bills as $value) {
                                        array_push($b_list, $value['id']);
                                    }
                                   // dd($b_list);
                                   
                                    $query->whereIn('bills.id',  $b_list );
                                })
                                // ->when($request->date, function ($query, $date) {
                                //     $query->whereBetween('receipt_records.created_at', [$date['startDate'].' 00:00:00', $date['endDate'].' 23:00:00']);
                                // },function($query){
                                //     $query->whereDate('receipt_records.created_at',Carbon::today());
                                // })
                                ->when($request->date, function ($query, $date) {
                                    if(isset($date['0']) && isset($date['1'])){
                                        if($date['0'] != null && $date['1'] != null){
                                            return $query->whereBetween('receipt_records.created_at', [$date['0'].' 00:00:00', $date['1'].' 23:00:00']);
                                        }
                                    }
                                    $query->whereDate('receipt_records.created_at',Carbon::today());
                                    
                                  
                                },function($query){
                                    
                                     $query->whereDate('receipt_records.created_at',Carbon::today());
                                })
                                ->sum('receipt_records.collected_amount');
                               
        $receipt_records = ReceiptRecord::join('customers','customers.id','=','receipt_records.customer_id')
                                ->join('invoices','invoices.id','=','receipt_records.invoice_id')
                                ->join('bills','bills.id','=','receipt_records.bill_id')
                                ->when($request->general, function ($query, $general) {
                                    $query->where(function ($query) use ($general) {
                                        $query->where('customers.name','LIKE', '%'.$general.'%')
                                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                                        ->orWhere('invoices.invoice_number', 'LIKE', '%' . $general . '%')
                                        ->orWhere('receipt_records.receipt_number', 'LIKE', '%' . $general . '%');
                                            });
                                    })
                              
                                ->when($request->bill_id, function ($query, $bills) {
                                    $b_list = array();
                                    foreach ($bills as $value) {
                                        array_push($b_list, $value['id']);
                                    }
                                   // dd($b_list);
                                    $query->whereIn('bills.id',  $b_list );
                                })
                                ->when($request->date, function ($query, $date) {
                                    if(isset($date['0']) && isset($date['1'])){
                                        if($date['0'] != null && $date['1'] != null){
                                            return $query->whereBetween('receipt_records.created_at', [$date['0'].' 00:00:00', $date['1'].' 23:00:00']);
                                        }
                                    }
                                    $query->whereDate('receipt_records.created_at',Carbon::today());
                                    
                                  
                                },function($query){
                                    
                                     $query->whereDate('receipt_records.created_at',Carbon::today());
                                })
                                ->select('bills.name as bill_name','invoices.bill_number','receipt_records.receipt_number','customers.ftth_id','receipt_records.issue_amount','receipt_records.collected_amount','receipt_records.month','receipt_records.year','receipt_records.created_at','receipt_records.receipt_date')
                                ->paginate(10);
      
                                $receipt_records->appends($request->all())->links();
        return Inertia::render("Client/DailyReceipt",
                [
                    'today_collection' => $today_collection,
                    'yesterday_collection' => $yesterday_collection,
                    'receipt_records'=>$receipt_records,
                    'bill_list'=>$bill_list,
                    'select_total'=>$select_total
                
                ]);
    }
    public function show(Request $request){
        return $this->index($request);
    }
}
