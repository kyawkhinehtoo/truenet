<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\BillAdjustment;
use App\Models\Package;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use App\Models\Bills;
use App\Models\Role;
use App\Models\User;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;
class BillAdjustmentController extends Controller
{
    //
    public function showAdjustmentBill(Request $request)
    {
        $roles = Role::get();
        $user = User::find(Auth::user()->id);
        if($request->bill_id){
            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $users = User::orderBy('name','ASC')->get();
         
            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

          
            $billings =  DB::table('bill_adjustment')
                ->join('customers', 'customers.id', '=', 'bill_adjustment.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->leftJoin('receipt_records','bill_adjustment.id','=','receipt_records.invoice_id')
                ->where(function($query){
                    return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
                    })
                ->where('bill_adjustment.bill_id', '=', $request->bill_id)
                ->when($request->keyword, function ($query, $search = null) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
                })->when($request->general, function ($query, $general) {
                    $query->where(function ($query) use ($general) {
                        $query->where('customers.name', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.email', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->total_payable_min, function ($query, $total_payable_min) {
                    $query->where('bill_adjustment.total_payable', '>=', $total_payable_min);
                })
         
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('bill_adjustment.total_payable', '<=', $total_payable_max);
                })
                ->when($request->installation, function ($query, $installation) {
                    $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                    $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                    $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
                })
                ->when($request->payment_type, function ($query, $payment_type) {
                    $type = ($payment_type == 1) ? 1 : 0;
                    $query->where('customers.payment_type', '=', $type);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
                })
                ->when($request->project, function ($query, $project) {
                    $query->where('customers.project_id', '=', $project);
                })
                ->when($request->township, function ($query, $township) {
                    $query->where('customers.township_id', '=', $township);
                })
                ->when($request->status, function ($query, $status) {
                    $query->where('customers.status_id', '=', $status);
                })
                ->when($orderform, function ($query, $orderform) {
                    $query->where('customers.order_form_sign_status', '=', $orderform['status']);
                })
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })
                ->when($request->adjustment_type, function ($query, $adjustment_type) {
                    $query->where('bill_adjustment.adjustment_type','=', $adjustment_type);
                })
                ->orderBy('bill_adjustment.id','DESC')
                ->select(
                'bill_adjustment.*')
                ->paginate(10);

                


                $current_bill = DB::table('bills')->where('id','=',$request->bill_id)->first();
          
            $billings->appends($request->all())->links();
            return Inertia::render('Client/showAdjustment', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'current_bill' => $current_bill,
                
            ]);
        }else{

            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $users = User::orderBy('name','ASC')->get();
            return Inertia::render('Client/showAdjustment', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
            ]);
        }
        
    }
    // public function checkOld($data){
    //     foreach ($data as $value) {
    //         //check previous 
    //         $billing =  DB::table('bill_adjustment')->where('invoice_id','=',$value['invoice_id'])
    //                      ->where('id','<',$value['id'])
    //                      ->first();
    //         if($billing){
    //             $value['total_payable'] = 
    //         }
    //     }
    // }
    public function newInvoice(Request $request){
        if($request->bill_id){
            $packages = Package::all();
            $bill = Bills::find($request->bill_id);
            $invoices_customers = DB::table('customers')->join('invoices','invoices.customer_id','=','customers.id')
                ->where('invoices.bill_id','=',$request->bill_id)
                ->pluck('customers.id');
                $new_customers = DB::table('customers')
                                        ->join('packages','packages.id','=','customers.package_id')
                                        ->join('status','status.id','=','customers.status_id')
                                        ->whereNotIn('customers.id',$invoices_customers)
                                        ->where(function($query){
                                            return $query->where('customers.deleted', '=', 0)
                                            ->orwherenull('customers.deleted');
                                            })
                                        ->select('customers.*','packages.name as package_name','packages.speed as package_speed','packages.type as package_type','packages.price as package_price','status.name as customer_status')
                                        ->get(); 
                $edit = false;
            return Inertia::render('Client/BillEdit', [
                'packages'=>$packages,
                'new_customers'=>$new_customers,
                'bill'=>$bill,
                'edit'=>$edit,
            ]);
        }
    }
    public function editInvoice(Request $request){
        if($request->invoice_id){
          
            $orginal_invoice = Invoice::select('invoices.id as invoice_id','invoices.*','receipt_records.id as receipt_id')
                                ->leftJoin('receipt_records','invoices.id','=','receipt_records.invoice_id')
                                ->find($request->invoice_id);
            $last_adjustment = BillAdjustment::select('bill_adjustment.*','receipt_records.id as receipt_id')
                                ->leftJoin('receipt_records','bill_adjustment.id','=','receipt_records.invoice_id')
                                ->where('bill_adjustment.invoice_id','=',$request->invoice_id)
                                ->latest('id')->first();
            $invoice = ($last_adjustment)?$last_adjustment:$orginal_invoice;
            $bill = Bills::find($invoice->bill_id);
            $edit = true;
         
            return Inertia::render('Client/BillEdit', [
                    'invoice'=>$invoice,
                    'bill'=>$bill,
                    'edit'=>$edit,
                ]);

        }
    }
    public function updateInvoice(Request $request)
    {

        if ($request->invoice_id) {
            $orginal_invoice = Invoice::select('invoices.id as invoice_id','invoices.*')->find($request->invoice_id);
            $last_adjustment = BillAdjustment::where('invoice_id','=',$request->invoice_id)->latest('id')->first();
            $invoice = ($last_adjustment)?$last_adjustment:$orginal_invoice;
            $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
            if($invoice->total_payable != $request->total_payable){
                $bill_adjustment = new BillAdjustment();
                $bill_adjustment->period_covered = $request->period_covered;
                $bill_adjustment->bill_number = $request->bill_number;
                $bill_adjustment->invoice_id = $invoice->invoice_id;
                $bill_adjustment->bill_id = $invoice->bill_id;
                $bill_adjustment->invoice_number = $invoice->invoice_number;

                $bill_adjustment->customer_id = $request->customer_id;
                $bill_adjustment->ftth_id = $request->ftth_id;
                $bill_adjustment->date_issued = $request->date_issued;
                $bill_adjustment->bill_to = $request->bill_to;
                
                $bill_adjustment->attn = $request->attn;
                $bill_adjustment->previous_balance = $request->previous_balance;
                $bill_adjustment->current_charge = $request->current_charge;
                $bill_adjustment->compensation = $request->compensation;
                $bill_adjustment->otc = $request->otc;
                $bill_adjustment->sub_total = $request->sub_total;
                $bill_adjustment->payment_duedate = $request->payment_duedate;
                $bill_adjustment->service_description = $request->service_description;
                $bill_adjustment->qty = $request->qty;
                $bill_adjustment->usage_days = $request->usage_days;
                $bill_adjustment->customer_status = $invoice->customer_status;
                $bill_adjustment->normal_cost = $request->normal_cost;
                $bill_adjustment->type = $request->type;
                $bill_adjustment->discount = $request->discount;
                $bill_adjustment->total_payable = $request->total_payable;
                $bill_adjustment->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($request->total_payable));
                $bill_adjustment->commercial_tax = $request->commercial_tax;
                $bill_adjustment->tax = $request->tax;
                $bill_adjustment->tax_to = $request->tax_to;
                $bill_adjustment->email = $request->email;
                $bill_adjustment->phone = $request->phone;
                $bill_adjustment->mail_sent_date = null;
                $bill_adjustment->sms_sent_date = null;
                $bill_adjustment->mail_sent_status = null;
                $bill_adjustment->sms_sent_status = null;
                $bill_adjustment->reminder_sms_sent_date = null;
                $bill_adjustment->reminder_sms_sent_status = null;

                $bill_adjustment->bill_month = $invoice->bill_month;
                $bill_adjustment->bill_year = $invoice->bill_year;
                //$bill_adjustment->coupon_id = $invoice->coupon_id;

                $invoice->mail_sent_date = null;
                $invoice->mail_sent_status = null;

                $invoice->sms_sent_date = null;
                $invoice->sms_sent_status = null;

                $invoice->file = null;
                $invoice->url = null;
                $bill_adjustment->adjustment_amount =  $request->total_payable - $invoice->total_payable;
           
                
                if($request->total_payable > $invoice->total_payable){
                    $bill_adjustment->adjustment_type = 'Debit Note'; 
                }else{
                    $bill_adjustment->adjustment_type = 'Credit Note'; 
                }

                //clear receipt transaction in both receipt record and receipt summery
                $receipt = ReceiptRecord::where('invoice_id','=',$invoice->invoice_id)->first();
                    if($receipt){
                        $receipt_id = $receipt->id;
                        ReceiptRecord::find($receipt_id)->delete();
                        $months = 12;
                        while($months > 0 ){
                         $status =  ReceiptSummery::where($months,'=',$receipt_id)
                            ->where('customer_id','=',$invoice->customer_id)
                            ->first();
                            if($status){
                                $status->$months = null;
                                $status->update();
                            }
                            $months--;
                        }
                    }
               $bill_adjustment->save();
               return redirect()->back()->with('message', 'Invoice Updated Successfully.');
            }else{
            $invoice->customer_id = $request->customer_id;
            $invoice->period_covered = $request->period_covered;
            $invoice->bill_number = $request->bill_number;
            $invoice->ftth_id = $request->ftth_id;
            $invoice->date_issued = $request->date_issued;
            $invoice->bill_to = $request->bill_to;
            $invoice->attn = $request->attn;
            $invoice->previous_balance = $request->previous_balance;
            $invoice->current_charge = $request->current_charge;
            $invoice->compensation = $request->compensation;
            $invoice->otc = $request->otc;
            $invoice->sub_total = $request->sub_total;
            $invoice->payment_duedate = $request->payment_duedate;
            $invoice->service_description = $request->service_description;
            $invoice->qty = $request->qty;
            $invoice->usage_days = $request->usage_days;
            $invoice->normal_cost = $request->normal_cost;
            $invoice->type = $request->type;
            $invoice->tax = $request->tax;
            $invoice->tax_to = $request->tax_to;
            $invoice->total_payable = $request->total_payable;
            $invoice->discount = $request->discount;
            $invoice->email = $request->email;
            $invoice->phone = $request->phone;
            if($request->reset_email){
                $invoice->mail_sent_date = null;
                $invoice->mail_sent_status = null;
            }
            if($request->reset_sms){
                $invoice->sms_sent_date = null;
                $invoice->sms_sent_status = null;
            }
            if($request->reset_pdf){
                $invoice->file = null;
                $invoice->url = null;
            }
            if($request->reset_receipt){
                //clear receipt transaction in both receipt record and receipt summery
                $receipt = ReceiptRecord::where('invoice_id','=',$invoice->invoice_id)->first();
                    if($receipt){
                        $receipt_id = $receipt->id;
                        ReceiptRecord::find($receipt_id)->delete();
                        $months = 12;
                        while($months > 0 ){
                         $status =  ReceiptSummery::where($months,'=',$receipt_id)
                            ->where('customer_id','=',$invoice->customer_id)
                            ->first();
                            if($status){
                                $status->$months = null;
                                $status->update();
                            }
                            $months--;
                        }
                    }
            }
            $invoice->update();
            
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
            }
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function createInvoice(Request $request){
        Validator::make($request->all(), [
            'customer_id' => 'required|max:255',
            'bill_id' => 'required|max:255',
            'period_covered' => 'required|max:255',
            'ftth_id' => 'required',
            'date_issued' => 'required',
            'bill_to' => 'required|max:255',
            'attn' => 'required|max:255',
            'current_charge' => 'required|max:255',
            'sub_total' => 'required|max:255',
            'payment_duedate' => 'required|max:255',
            'service_description' => 'required|max:255',
            'qty' => 'required|max:255',
            'usage_days' => 'required|max:255',
            'normal_cost' => 'required|max:255',
            'type' => 'required|max:255',
            'total_payable' => 'required|max:255',
            'phone' => 'required|max:255',
            

        ])->validate();
         //   dd($request);
        $bill = Bills::find($request->bill_id);
        $max_invoice_id =  DB::table('invoices')
                                        ->where('invoices.bill_id', '=', $request->bill_id)
                                        ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                                        ->first();
        $customer_status = Customer::join('status','status.id','=','customers.status_id')
                                    ->join('packages','packages.id','=','customers.package_id')
                    ->where('customers.id','=',$request->customer_id)
                    ->select('status.name as status_name','packages.type as package_type')
                    ->first();

        $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $invoice = new Invoice();
        $invoice->customer_id = $request->customer_id;
        $invoice->bill_id = $request->bill_id;
        $invoice->invoice_number =($max_invoice_id)?($max_invoice_id->max_invoice_number + 1) : 1;
        $invoice->period_covered = $request->period_covered['0'].' to '.$request->period_covered['1'];
        $invoice->bill_number = strtoupper($bill->bill_number . "-" . substr(trim($request->ftth_id['ftth_id']), 0, 5) . "-" . $customer_status->package_type);
        $invoice->ftth_id = $request->ftth_id['ftth_id'];
        $invoice->date_issued = $request->date_issued;
        $invoice->bill_to = $request->bill_to;
        $invoice->attn = $request->attn;
        $invoice->previous_balance = $request->previous_balance;
        $invoice->current_charge = $request->current_charge;
        $invoice->compensation = $request->compensation;
        $invoice->otc = $request->otc;
        $invoice->sub_total = $request->sub_total;
        $invoice->payment_duedate = $request->payment_duedate;
        $invoice->service_description = $request->service_description;
        $invoice->qty = $request->qty;
        $invoice->usage_days = $request->usage_days;
        $invoice->normal_cost = $request->normal_cost;
        $invoice->type = $request->type;
        $invoice->tax = $request->tax;
        $invoice->tax_to = $request->tax_to;
        $invoice->total_payable =0;
        $invoice->discount = $request->discount;
        $invoice->email = $request->email;
        $invoice->phone = $request->phone;
        $invoice->customer_status = $customer_status->status_name;
        $invoice->bill_month = $bill->bill_month;
        $invoice->bill_year = $bill->bill_year;
        $invoice->amount_in_word = null;
        $invoice->commercial_tax = "The Prices are inclusive of Commerial Tax (15%)";
        $invoice->save();


        $bill_adjustment = new BillAdjustment();
        $bill_adjustment->period_covered =  $request->period_covered['0'].' to '.$request->period_covered['1'];
        $bill_adjustment->bill_number = $invoice->bill_number;
        $bill_adjustment->invoice_id = $invoice->id;
        $bill_adjustment->bill_id = $invoice->bill_id;
        $bill_adjustment->invoice_number = $invoice->invoice_number;
        $bill_adjustment->customer_id = $request->customer_id;
        $bill_adjustment->ftth_id = $request->ftth_id['ftth_id'];
        $bill_adjustment->date_issued = $request->date_issued;
        $bill_adjustment->bill_to = $request->bill_to;
        
        $bill_adjustment->attn = $request->attn;
        $bill_adjustment->previous_balance = $request->previous_balance;
        $bill_adjustment->current_charge = $request->current_charge;
        $bill_adjustment->compensation = $request->compensation;
        $bill_adjustment->otc = $request->otc;
        $bill_adjustment->sub_total = $request->sub_total;
        $bill_adjustment->payment_duedate = $request->payment_duedate;
        $bill_adjustment->service_description = $request->service_description;
        $bill_adjustment->qty = $request->qty;
        $bill_adjustment->usage_days = $request->usage_days;
        $bill_adjustment->customer_status = $invoice->customer_status;
        $bill_adjustment->normal_cost = $request->normal_cost;
        $bill_adjustment->type = $request->type;
        $bill_adjustment->discount = $request->discount;
        $bill_adjustment->total_payable = $request->total_payable;
        $bill_adjustment->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($request->total_payable));
        $bill_adjustment->commercial_tax = $request->commercial_tax;
        $bill_adjustment->tax = $request->tax;
        $bill_adjustment->tax_to = $request->tax_to;
        $bill_adjustment->email = $request->email;
        $bill_adjustment->phone = $request->phone;
        $bill_adjustment->mail_sent_date = null;
        $bill_adjustment->sms_sent_date = null;
        $bill_adjustment->mail_sent_status = null;
        $bill_adjustment->sms_sent_status = null;
        $bill_adjustment->reminder_sms_sent_date = null;
        $bill_adjustment->reminder_sms_sent_status = null;
        $bill_adjustment->bill_month = $invoice->bill_month;
        $bill_adjustment->bill_year = $invoice->bill_year;
        $bill_adjustment->adjustment_amount =  $request->total_payable;
        $bill_adjustment->adjustment_type = "Debit Note";
        $bill_adjustment->save();
        return redirect()->back()->with('message', 'Invoice Created Successfully.');
    }
    public function testProcess(Request $request){
       if(isset($request->name)){
           sleep(60);
       }
        return Inertia::render('Test');
    }
}
