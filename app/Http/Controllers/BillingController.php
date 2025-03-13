<?php

namespace App\Http\Controllers;

use App\Models\BillAdjustment;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;
use App\Models\Bills;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Status;
use App\Models\CustomerHistory;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Role;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use ArrayIterator;
use CachingIterator;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Storage;
use Mail;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Traits\PdfTrait;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;
use App\Models\SmsGateway;
use App\Jobs\PDFCreateJob;
use App\Jobs\BillingSMSJob;
use App\Jobs\ReminderSMSJob;

class BillingController extends Controller
{
    use PdfTrait, MarkupTrait, SMSTrait;
    public function BillGenerator()
    {

        $packages = Package::select('packages.*')
            ->orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $bill = Bills::get();
        return Inertia::render('Client/BillGenerator', [
            'packages' => $packages,
            'townships' => $townships,
            'bill' => $bill,
        ]);
    }
    public function doGenerate(Request $request)
    {


        if (!RadiusController::checkRadiusEnable()) {
            return redirect()->back()->with('message', 'Billing Cannot Generate Due to Radius Issue');
        }

        $bill_year = $request->bill_year;
        $bill_month = $request->bill_month;
        $bill_number = $request->bill_number;

        BillingTemp::truncate();
        $expiredList = RadiusController::getExpiredAll($bill_month, $bill_year);
        $expiredList = json_decode($expiredList);
        $expiredUsers = array_column($expiredList, "username");
        $cal_days = cal_days_in_month(CAL_GREGORIAN, $bill_month, $bill_year);
        $temp_date = Date('Y-m-d', strtotime($cal_days . '-' . $bill_month . '-' . $bill_year));
        $customers =  DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->when($request->bill_id, function ($query, $bill) {
                $bill_id = $bill['id'];

                $query_result  = DB::select('select customer_id from invoices where bill_id =' . $bill_id);
                $result = collect($query_result)->pluck('customer_id')->toArray();

                $query->whereNotIn('customers.id', $result);
            })
            ->whereDate('customers.installation_date', '<', $temp_date)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotIn('status.type', ['new', 'pending', 'cancel'])
            // ->whereNotIn('customers.pppoe_account', $expiredUsers)
            //->where('customers.ftth_id','=','TCL00009-FTTH')//just for debugging
            ->select(
                'customers.id as id',
                'customers.ftth_id as ftth_id',
                'customers.name as name',
                'customers.order_date as order_date',
                'customers.phone_1 as phone_1',
                'customers.phone_2 as phone_2',
                'customers.email',
                'customers.address as address',
                'customers.installation_date as installation_date',
                'customers.advance_payment as advance_payment',
                'customers.advance_payment_day as advance_payment_day',
                'customers.status_id as status_id',
                'townships.name as township',
                'packages.name as package',
                'packages.type as type',
                'packages.speed as speed',
                'packages.price as price',
                'packages.currency as currency',
                'status.name as status'
            )
            ->get();
        if ($customers) {

            foreach ($customers as $value) {

                $billing_cost = $value->price;
                $total_cost = round($billing_cost, 2);

                // $max_invoice_no =  DB::table('invoices')
                //     ->where('invoices.bill_id', '=', $bill->id)
                //     ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                //     ->pluck('max_invoice_number')
                //     ->first();

                // Create the range


                if ($billing_cost <> 0) {
                    $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);

                    $billing = new BillingTemp();
                    $billing->bill_number = $bill_number;
                    $billing->customer_id = $value->id;
                    $billing->ftth_id = $value->ftth_id;
                    $billing->date_issued = $request->issue_date;
                    $billing->bill_to =  $value->name;
                    $billing->attn =  $value->address;
                    $billing->previous_balance = 0; // always zero 
                    $billing->current_charge = $total_cost;
                    $billing->compensation = 0;
                    $billing->otc = 0;
                    $billing->sub_total = $total_cost;
                    $billing->payment_duedate = $request->due_date;
                    $billing->service_description = $value->package;
                    $billing->type = "Prepaid";
                    $billing->qty = $value->speed . " Mbps";
                    $billing->usage_day = 0;
                    $billing->usage_month = 1;
                    $billing->bonus_day = 0;
                    $billing->bonus_month = 0;

                    $billing->customer_status = $value->status;
                    $billing->normal_cost = $value->price;
                    $billing->total_payable = $total_cost;
                    $billing->discount = 0;
                    $billing->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($total_cost));
                    $billing->commercial_tax = "The Prices are inclusive of Commerial Tax (15%)";
                    $billing->phone = trim($value->phone_1);
                    $billing->email = $value->email;
                    $billing->bill_month =  $bill_month;
                    $billing->bill_year =  $bill_year;
                    $billing->save();
                }
            }
            //return redirect()->route('tempBilling');
            return redirect()->back()->with('message', 'Billing Created Successfully.');
        }



        return redirect()->back()->with('message', 'Billing Cannot Generate');
    }

    public function goTemp(Request $request)
    {
        //$billings = Billing::paginate(10);

        $packages = Package::orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $status = Status::get();
        $bill = Bills::where('status', 'active')->get();
        $billings =  DB::table('temp_billings')
            ->join('customers', 'customers.id', '=', 'temp_billings.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
            })
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })
            ->when($request->total_payable_min, function ($query, $total_payable_min) {
                $query->where('temp_billings.total_payable', '>=', $total_payable_min);
            })
            ->when($request->total_payable_max, function ($query, $total_payable_max) {
                $query->where('temp_billings.total_payable', '<=', $total_payable_max);
            })
            ->when($request->township, function ($query, $township) {
                $query->where('customers.township_id', '=', $township);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'temp_billings.id';
                if ($sort == 'cid') {
                    $sort_by = 'temp_billings.id';
                }

                $query->orderBy($sort_by, 'asc');
            }, function ($query) {
                $query->orderBy('temp_billings.id', 'asc');
            })
            ->select('temp_billings.*')
            ->paginate(20);
        $billings->appends($request->all())->links();
        return Inertia::render('Client/TempBilling', [
            'packages' => $packages,
            'townships' => $townships,
            'status' => $status,
            'billings' => $billings,
            'bill' => $bill,
        ]);
    }
    public function checkStartDate($month, $year, $customer_id, $price)
    {
        //check suspense/terminate by suspense terminate date with bill issue month (plan changed user must adjust manually)

        //check bill start date fallback to installation date 
        // get total days of months and substract from the total days of bill start date to current day 
        // if under dayofmonth 
        // calculate bill start date/installation date to the end of the month by 
        // total bill - actual bill = compensation 
        // bill month/ bill day ? 
        //Customer Status 
        //4 = Suspense
        //5 = Terminate

        $billing_day = "0";
        $total_cost = $price;
        $customer = Customer::find($customer_id);
        $installation_date = $customer->installation_date;
        $bill_date = $installation_date;

        $customer_history = CustomerHistory::where('customer_id', '=', $customer_id)
            ->where('active', '=', 1)
            ->first();
        if ($customer_history) {
            $bill_date = ($bill_date >= $customer_history->start_date) ? $bill_date : $customer_history->start_date;

            if ($customer_history->status_id == 4 || $customer_history->status_id == 5) {
                //customer has history
                return $this->endDateCompare($customer_history->start_date, $year, $month, $price);
            } else {
                //need to check active date
                if ($customer_history->start_date) {

                    return $this->startDateCompare($bill_date, $year, $month, $price);
                }
                return $this->startDateCompare($bill_date, $year, $month, $price);
                // $billing_day = "1 Month";
                // $total_cost = $price;
                // return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
        } else {
            if ($customer->status_id == 2) {
                return $this->startDateCompare($bill_date, $year, $month, $price);
            } else {
                $billing_day = "";
                $total_cost = 0;
                return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
            }
        }
    }
    public function endDateCompare($bill_date, $year, $month, $price)
    {
        $billing_day = "0";
        $total_cost = $price;
        $stop_month = (int)date("n", strtotime($bill_date));
        $stop_year = (int)date("Y", strtotime($bill_date));

        if ($stop_year <= $year) {
            if ($bill_date != null) {
                if ($stop_month == $month) {

                    //sus or ter month is the same with billing month
                    $billing_day_temp = date("j", strtotime($bill_date)) - 1;

                    $billing_day = $billing_day_temp . " Days";
                    $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                    $cost_per_day = $price / $cal_days;
                    $total_cost = $cost_per_day * $billing_day_temp;
                } elseif ($stop_month > $month) {
                    $billing_day = "1 Month";
                    $total_cost = $price;
                } else {
                    $billing_day = "0";
                    $total_cost = 0;
                }
            } else {
                $billing_day = "";
                $total_cost = 0;
            }
        } else {
            $billing_day = "1 Month";
            $total_cost = $price;
        }
        return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
    }
    public function startDateCompare($bill_date, $year, $month, $price)
    {
        $billing_day = "0";
        $total_cost = $price;
        $start_month = (int)date("n", strtotime($bill_date));
        $start_year = (int)date("Y", strtotime($bill_date));

        if ($start_year == $year) {
            if ($bill_date != null) {
                if ($start_month == $month) {
                    $billing_day_temp = date("j", strtotime($bill_date));
                    if ($billing_day_temp == 0) {
                        $billing_day = "1 Month";
                        $total_cost = $price;
                    } else {
                        //active or reactive date is the same with billing month
                        $cal_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                        $billing_day_temp = $cal_days - $billing_day_temp;
                        $billing_day = $billing_day_temp . " Days";
                        $cost_per_day = $price / $cal_days;
                        $total_cost = $cost_per_day * $billing_day_temp;
                    }
                } elseif ($start_month < $month) {
                    $billing_day = "1 Month";
                    $total_cost = $price;
                } else {
                    $billing_day = "0";
                    $total_cost = 0;
                }
            } else {
                $billing_day = "";
                $total_cost = 0;
            }
        } else {
            $billing_day = "1 Month";
            $total_cost = $price;
        }
        return array('total_cost' => $total_cost, 'billing_day' => $billing_day);
    }
    public function updateTemp(Request $request)
    {

        if ($request->id) {
            $temp_billing = BillingTemp::find($request->id);
            $temp_billing->customer_id = $request->customer_id;
            $temp_billing->start_date = $request->start_date;
            $temp_billing->end_date = $request->end_date;
            $temp_billing->bill_number = $request->bill_number;
            $temp_billing->ftth_id = $request->ftth_id;
            $temp_billing->date_issued = $request->date_issued;
            $temp_billing->bill_to = $request->bill_to;
            $temp_billing->attn = $request->attn;

            $temp_billing->current_charge = $request->current_charge;
            $temp_billing->compensation = $request->compensation;
            $temp_billing->public_ip = $request->public_ip;
            $temp_billing->otc = $request->otc;
            $temp_billing->sub_total = $request->sub_total;
            $temp_billing->payment_duedate = $request->payment_duedate;
            $temp_billing->service_description = $request->service_description;
            $temp_billing->qty = $request->qty;
            $temp_billing->usage_day = $request->usage_day;
            $temp_billing->usage_month = $request->usage_month;
            $temp_billing->bonus_day = $request->bonus_day;
            $temp_billing->bonus_month = $request->bonus_month;
            $temp_billing->normal_cost = $request->normal_cost;
            $temp_billing->type = $request->type;
            $temp_billing->tax = $request->tax;
            $temp_billing->total_payable = $request->total_payable;
            $temp_billing->discount = $request->discount;
            $temp_billing->phone = $request->phone;
            $temp_billing->update();
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function updateInvoice(Request $request)
    {


        if ($request->id) {
            $invoice = Invoice::find($request->id);
            $invoice->customer_id = $request->customer_id;
            $invoice->start_date = $request->start_date;
            $invoice->end_date = $request->end_date;
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
            //  $invoice->popsite_id = $request->package['pop_id'];

            $invoice->qty = $request->qty;
            $invoice->usage_day = $request->usage_day;
            $invoice->usage_month = $request->usage_month;
            $invoice->bonus_day = $request->bonus_day;
            $invoice->bonus_month = $request->bonus_month;
            $invoice->normal_cost = $request->normal_cost;
            $invoice->type = $request->type;
            $invoice->tax = $request->tax;
            $invoice->public_ip = $request->public_ip;
            $invoice->total_payable = $request->total_payable;
            $invoice->discount = $request->discount;
            $invoice->phone = $request->phone;

            if ($request->reset_email) {
                $invoice->sent_date = null;
                $invoice->mail_sent_status = null;
            }
            if ($request->reset_sms) {
                $invoice->sent_date = null;
                $invoice->sms_sent_status = null;
            }

            $invoice->invoice_file = null;
            $invoice->invoice_url = null;

            //if ($request->reset_receipt) {
            
            $invoice_no = "INV" . substr($invoice->bill_number, 0, 4) . str_pad($invoice->invoice_number, 5, "0", STR_PAD_LEFT);
            $receipt = ReceiptRecord::where('invoice_id', '=', $invoice->id)->first();
            if ($receipt) {
                $receipt_id = $receipt->id;
                ReceiptRecord::find($receipt_id)->delete();
                $months = 12;
                while ($months > 0) {
                    $status =  ReceiptSummery::where($months, '=', $receipt_id)
                        ->where('customer_id', '=', $invoice->customer_id)
                        ->first();
                    if ($status) {
                        $status->$months = null;
                        $status->update();
                    }
                    $months--;
                }
                $receipt->delete();
                activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($invoice)
                ->log('Receipt Reset. Customer ID: ' . $invoice->ftth_id . ', Invoice No. : ' . $invoice_no);
            }
            $old_c = Customer::find($request->customer_id);

            if ($request->package['id'] != $old_c->package_id || $request->attn != $old_c->address) {
                $new_history = new CustomerHistory();
                $new_history->customer_id = $request->customer_id;
                $new_history->actor_id = Auth::user()->id;
                if ($request->package) {
                    if ($request->package['id'] != $old_c->package_id) {
                        $new_history->type = 'plan_change';
                        $new_history->new_package = $request->package['id'];
                        $new_history->old_package = $old_c->package_id;
                        $myDateTime = new DateTime;
                        if ($request->start_date) {
                            $myDateTime = new DateTime($request->start_date);
                        }
                        $newtime = clone $myDateTime;
                        $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    }
                }
                if ($request->attn != $old_c->address) {
                    $new_history->type = 'relocation';
                    //new
                    if ($request->attn)
                        $new_history->new_address = $request->attn;
                    $new_history->old_address = $old_c->address;
                }
                $new_history->active = 1;
                $new_history->date = date("Y-m-j h:m:s");
                $new_history->save();
            }
            $customer = Customer::find($request->customer_id);
            $customer->name = $request->bill_to;
            $customer->address = $request->attn;
            $customer->package_id = $request->package['id'];


            if ($request->phone) {

                $phones = $billing_phone = preg_replace('/\s+/', '', $request->phone);
                if (strpos($billing_phone, ',') !== false) {
                    $phones = explode(",", $billing_phone);
                }
                if (strpos($billing_phone, ';') !== false) {
                    $phones = explode(';', $billing_phone);
                }
                if (strpos($billing_phone, ':') !== false) {
                    $phones = explode(':', $billing_phone);
                }
                if (strpos($billing_phone, ' ') !== false) {
                    $phones = explode(' ', $billing_phone);
                }
                if (strpos($billing_phone, '/') !== false) {
                    $phones = explode('/', $billing_phone);
                }
                //possible phone number style  
                // 09420043911
                // 9420043911
                // 959420043911
                // +959420043911
                $pattern = "/^(09|\+959)+[0-9]+$/";

                if (is_array($phones)) {
                    $phones = array_map('trim', $phones); //first remove white space from array value
                    $phones = array_filter($phones); //get rid of empty value from array then
                    $phones = array_values($phones); // reindexing the array
                    $phone_1 = trim($phones[0]);
                    $phone_2 = trim($phones[1]);

                    $customer->phone_1 = $this->sanitisePhone($phone_1);
                    $customer->phone_2 = $this->sanitisePhone($phone_2);
                } else {
                    $customer->phone_1 =  $this->sanitisePhone($phones);
                }
            }



            $customer->update();
            if (RadiusController::checkRadiusEnable()) {
                RadiusController::updateRadius($customer->id);
            }


            $original = $invoice->getOriginal();  // Get the original values before update
            $invoice->update();                   // Perform the update
            $changes = $invoice->getChanges();    // Get the updated values after the update

            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'from' => $original[$key] ?? null,  // Original value
                    'to' => $newValue                   // New value
                ];
            }
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($invoice)
                ->withProperties(['changes' => $logData])  // Log the changes with from-to values
                ->log('Invoice updated. Customer ID: ' . $invoice->ftth_id . ', Invoice No. : ' . $invoice_no);
            return redirect()->back()->with('message', 'Invoice Updated Successfully.');
        }
        return redirect()->back()->with('message', 'Invoice Cannot be Updated.');
    }
    public function createInvoice(Request $request)
    {
        Validator::make($request->all(), [
            'customer_id' => 'required|max:255',
            'bill_id' => 'required|max:255',
            'start_date' => 'required|max:255',
            'end_date' => 'required|max:255',
            'ftth_id' => 'required',
            'date_issued' => 'required',
            'bill_to' => 'required|max:255',
            'attn' => 'required|max:255',
            'current_charge' => 'required|max:255',
            'sub_total' => 'required|max:255',
            'payment_duedate' => 'required|max:255',
            'service_description' => 'required|max:255',
            'qty' => 'required|max:255',
            'usage_month' => 'required|max:255',
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
        $customer_status = Customer::join('status', 'status.id', '=', 'customers.status_id')
            ->join('packages', 'packages.id', '=', 'customers.package_id')
            ->where('customers.id', '=', $request->customer_id)
            ->select('status.name as status_name', 'packages.type as package_type')
            ->first();
        $inWords = new NumberFormatter('en', NumberFormatter::SPELLOUT);
        $invoice = new Invoice();
        $invoice->customer_id = $request->customer_id;
        $invoice->bill_id = $request->bill_id;
        $invoice->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
        $invoice->start_date = $request->start_date;
        $invoice->end_date = $request->end_date;
        $invoice->bill_number = $bill->bill_number;
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
        $invoice->usage_day = $request->usage_day;
        $invoice->usage_month = $request->usage_month;
        $invoice->bonus_day = $request->bonus_day;
        $invoice->bonus_month = $request->bonus_month;
        $invoice->normal_cost = $request->normal_cost;
        $invoice->type = $request->type;
        $invoice->tax = $request->tax;
        $invoice->public_ip = $request->public_ip;
        $invoice->total_payable = $request->total_payable;
        $invoice->discount = $request->discount;
        $invoice->email = $request->email;
        $invoice->phone = $request->phone;
        $invoice->customer_status = $customer_status->status_name;
        $invoice->bill_month = $bill->bill_month;
        $invoice->bill_year = $bill->bill_year;
        //    $invoice->popsite_id = $request->package['pop_id'];
        $invoice->amount_in_word = 'Amount in words: ' . ucwords($inWords->format($request->total_payable));
        $invoice->commercial_tax = "The Prices are inclusive of Commerial Tax (5%)";
        $invoice->save();
        return redirect()->back()->with('message', 'Invoice Created Successfully.');
    }
    public function preview_1(Request $request)
    {
        $billings = BillingTemp::find($request->id);
        return view('preview', $billings);
    }
    public function preview_2(Request $request)
    {

        $billing_invoice = Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->leftjoin('users', 'users.id', '=', 'receipt_records.receipt_person')
            ->join('customers', 'receipt_records.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('receipt_records.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type', 'receipt_records.remark as remark', 'receipt_records.collected_amount as collected_amount', 'receipt_records.receipt_date as receipt_date', 'receipt_records.receipt_number as receipt_number', 'users.name as collector')
            ->first();

        return view('voucher', $billing_invoice);
    }
    public function showInvoice(Request $request)
    {

        $billing_invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();

        return view('invoice', $billing_invoice);
    }

    public function saveSingle(Request $request)
    {
        //   dd($request);
        if ($request->bill_number && $request->ftth_id) {
            $temp = BillingTemp::where('ftth_id', 'LIKE', '%' . $request->ftth_id . '%')->first();
            $bill = Bills::where('name', 'LIKE', '%' . $request->bill_number . '%')
                ->first();
            if ($temp && $bill) {
                $max_invoice_id =  DB::table('invoices')
                    ->where('invoices.bill_id', '=', $bill->id)
                    ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                    ->first();

                $billing = new Invoice();
                $billing->period_covered = $temp->period_covered;
                $billing->bill_number = $temp->bill_number;
                $billing->bill_id = $bill->id;
                $billing->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
                $billing->customer_id = $temp->customer_id;
                $billing->ftth_id = $temp->ftth_id;
                $billing->date_issued = $temp->date_issued;
                $billing->bill_to = $temp->bill_to;
                $billing->attn = $temp->attn;
                $billing->previous_balance = $temp->previous_balance;
                $billing->current_charge = $temp->current_charge;
                $billing->compensation = $temp->compensation;
                $billing->otc = $temp->otc;
                $billing->sub_total = $temp->sub_total;
                $billing->payment_duedate = $temp->payment_duedate;
                $billing->service_description = $temp->service_description;
                $billing->qty = $temp->qty;
                $billing->usage_days = $temp->usage_days;
                $billing->customer_status = $temp->customer_status;
                $billing->normal_cost = $temp->normal_cost;
                $billing->type = $temp->type;
                $billing->total_payable = $temp->total_payable;
                $billing->discount = $temp->discount;
                $billing->amount_in_word = $temp->amount_in_word;
                $billing->commercial_tax = $temp->commercial_tax;
                $billing->tax = $temp->tax;
                $billing->phone = $temp->phone;
                $billing->bill_month = $temp->bill_month;
                $billing->bill_year = $temp->bill_year;
                $billing->save();
            }
        }
    }
    public function saveFinal(Request $request)
    {

        if ($request->has('bill_name') || $request->has('bill_id')) {

            $existingBill = isset($request->bill_id['id']) ? $request->bill_id['id'] : null;
            $bill = ($request->additonal && $existingBill) ? Bills::find($existingBill) : new Bills();
            $bill_data = BillingTemp::first();
            if (!$request->additonal) {
                $bill->name = $request->bill_name;
                $bill->bill_number = substr($bill_data->bill_number, 0, 4);
                $bill->bill_month = $bill_data->bill_month;
                $bill->bill_year = $bill_data->bill_year;
                $bill->status = "active";
                $bill->save();
            }

            $invoices = Invoice::where('bill_id', $bill->id)->select('customer_id')->pluck('customer_id')->toArray();

            $temp = null;
            if ($request->additonal && $existingBill && $invoices) {
                $temp = BillingTemp::whereNotIn('customer_id', $invoices)->get();
            } else {
                $temp = BillingTemp::all();
            }

            foreach ($temp as $value) {
                $max_invoice_id =  DB::table('invoices')
                    ->where('invoices.bill_id', '=', $bill->id)
                    ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                    ->pluck('max_invoice_number')
                    ->first();
                if ($value->total_payable > 0) {
                    $billing = new Invoice();
                    $billing->start_date = $value->start_date;
                    $billing->end_date = $value->end_date;
                    $billing->bill_number = $value->bill_number;
                    $billing->bill_id = $bill->id;
                    $billing->invoice_number = ($max_invoice_id) ? ($max_invoice_id + 1) : 1;
                    $billing->customer_id = $value->customer_id;
                    $billing->ftth_id = $value->ftth_id;
                    $billing->date_issued = $value->date_issued;
                    $billing->bill_to = $value->bill_to;
                    $billing->attn = $value->attn;
                    $billing->public_ip = $value->public_ip;
                    $billing->current_charge = $value->current_charge;
                    $billing->compensation = $value->compensation;
                    $billing->otc = $value->otc;
                    $billing->sub_total = $value->sub_total;
                    $billing->payment_duedate = $value->payment_duedate;
                    $billing->service_description = $value->service_description;
                    // $billing->popsite_id = $value->popsite_id;
                    $billing->qty = $value->qty;
                    $billing->usage_day = $value->usage_day;
                    $billing->usage_month = $value->usage_month;
                    $billing->bonus_day = $value->bonus_day;
                    $billing->bonus_month = $value->bonus_month;
                    $billing->customer_status = $value->customer_status;
                    $billing->normal_cost = $value->normal_cost;
                    $billing->type = $value->type;
                    $billing->total_payable = $value->total_payable;
                    $billing->discount = $value->discount;
                    $billing->amount_in_word = $value->amount_in_word;
                    $billing->commercial_tax = $value->commercial_tax;
                    $billing->tax = $value->tax;
                    $billing->phone = $value->phone;
                    $billing->email = $value->email;
                    $billing->bill_month = $value->bill_month;
                    $billing->bill_year = $value->bill_year;
                    $billing->save();
                }
            }
            // activity()
            //     ->causedBy(User::find(Auth::id()))
            //     ->performedOn($invoices)
            //     ->log('Bill Save Final. Bill No. :' .   $bill->name);
            return redirect()->back()->with('message', 'Billing Generated Successfully');
        }
    }
    public function showList()
    {
        $lists = Bills::all();
        return Inertia::render('Client/BillList', [
            'lists' => $lists
        ]);
    }
    public function showBill(Request $request)
    {
        $roles = Role::get();
        $user = User::join('roles', 'users.role', 'roles.id')
            ->select('users.*', 'roles.delete_invoice')
            ->where('users.id', '=', Auth::user()->id)
            ->first();
        if ($request->bill_id) {
            $lists = Bills::all();
            $packages =  Package::select('packages.*')
                ->orderBy('price', 'ASC')->get();
          
            $package_type = Package::select('type')
                ->groupBy('type')
                ->orderBy('type', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::join('roles', 'users.role', 'roles.id')
                ->where('roles.name', 'LIKE', '%Sale%')
                ->select('users.*')
                ->orderBy('users.name', 'ASC')->get();

            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

            $max_receipt =  DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('max(receipt_records.receipt_number) as max_receipt_number'))
                ->first();
            $total_receivable = DB::table('invoices')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('sum(invoices.total_payable) as total_payable'))
                ->first();
            $receivable = $total_receivable->total_payable;

            $total_paid = DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('sum(receipt_records.collected_amount) as paid'))
                ->first();
            $paid = $total_paid->paid;

            $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
                ->select('invoice_id')
                ->get()
                ->toArray();
            $last_receipt = ReceiptRecord::join('invoices', 'receipt_records.invoice_id', '=', 'invoices.id')
                ->groupBy('invoices.customer_id')
                ->select(DB::raw('max(receipt_records.id) as id'))
                ->get()
                ->toArray();
            $last_invoices = Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
                ->whereIn('receipt_records.id', $last_receipt)
                ->select('invoices.id', 'invoices.customer_id', 'invoices.period_covered')
                ->get();
            //select i.period_covered, i.ftth_id from invoices i join receipt_records rr on i.id = rr.invoice_id where rr.id in (select max(rr.id) as received_id from invoices i left join receipt_records rr on i.id = rr.invoice_id group by i.customer_id) and i.ftth_id = 'ggh0102';

            $billings =  DB::table('invoices')->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orWhereNull('customers.deleted');
                })
                ->where('invoices.bill_id', '=', $request->bill_id)
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
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->total_payable_min, function ($query, $total_payable_min) {
                    $query->where('invoices.total_payable', '>=', $total_payable_min);
                })
                ->when($request->payment_type, function ($query, $payment_type) use ($receipts) {
                    if ($payment_type == 'unpaid') {
                        $query->whereNotIn('invoices.id', $receipts);
                    } else {
                        $query->whereIn('invoices.id', $receipts);
                    }
                })
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
                })
                ->when($request->installation, function ($query, $installation) {
                    $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                    $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                    $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
                })
                ->when($request->package, function ($query, $package) {
                    $query->where('customers.package_id', '=', $package);
                })
                ->when($request->package_speed, function ($query, $package_speed) {
                    $speed_type =  explode("|", $package_speed);
                    $speed = $speed_type[0];
                    $type = $speed_type[1];
                    $query->where('packages.speed', '=', $speed);
                    $query->where('packages.type', '=', $type);
                })
                ->when($request->package_type, function ($query, $package_type) {
                    $query->where('packages.type', '=', $package_type);
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

                ->select(
                    'invoices.id as id',
                    'invoices.bill_id as bill_id',
                    'invoices.start_date as start_date',
                    'invoices.end_date as end_date',
                    'invoices.customer_id as customer_id',
                    'invoices.period_covered as period_covered',
                    'invoices.bill_to as bill_to',
                    'invoices.bill_number as bill_number',
                    'invoices.ftth_id as ftth_id',
                    'invoices.date_issued as date_issued',
                    'invoices.attn as attn',
                    'invoices.service_description as service_description',
                    'invoices.total_payable as total_payable',
                    'invoices.bill_year as bill_year',
                    'invoices.bill_month as bill_month',
                    'invoices.amount_in_word as amount_in_word',
                    'invoices.qty as qty',
                    'invoices.usage_day as usage_day',
                    'invoices.usage_month as usage_month',
                    'invoices.bonus_day as bonus_day',
                    'invoices.bonus_month as bonus_month',
                    'invoices.invoice_file as invoice_file',
                    'invoices.invoice_url as invoice_url',
                    'invoices.sent_date as sent_date',
                    'invoices.payment_duedate as payment_duedate',
                    'invoices.previous_balance as previous_balance',
                    'invoices.current_charge as current_charge',
                    'invoices.sub_total as sub_total',
                    'invoices.normal_cost as normal_cost',
                    'invoices.otc as otc',
                    'invoices.type as type',
                    'invoices.compensation as compensation',
                    'invoices.discount as discount',
                    'invoices.tax as tax',
                    'invoices.public_ip as public_ip',
                    'invoices.phone as phone',
                    'invoices.sms_sent_status as sms_sent_status',
                    'receipt_records.collected_currency as currency',
                    'receipt_records.id as receipt_id',
                    'receipt_records.receipt_number as receipt_number',
                    'receipt_records.receipt_file as receipt_file',
                    'receipt_records.receipt_url',
                    'receipt_records.status as receipt_status',
                    DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date'),
                    'receipt_records.collected_person as collected_person',
                    'receipt_records.collected_amount as collected_amount',
                    'receipt_records.transition as transition',
                    'receipt_records.remark as remark',
                    'receipt_records.payment_channel as payment_channel',


                )
                ->orderBy('invoices.id')
                ->paginate(10);
            //DATE_FORMAT(date_and_time, '%Y-%m-%dT%H:%i') AS 
            $invoices_customers = DB::table('customers')->join('invoices', 'invoices.customer_id', '=', 'customers.id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->pluck('customers.id');
            $prepaid_customers = DB::table('customers')
                ->join('packages', 'packages.id', '=', 'customers.package_id')
                ->join('status', 'status.id', '=', 'customers.status_id')
                ->leftjoin('receipt_records as rr', 'customers.id', '=', 'rr.customer_id')
                ->whereNotIn('customers.id', $invoices_customers)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->select('customers.*', 'customers.id as customer_id', 'packages.name as package_name', 'packages.speed as package_speed', 'packages.type as package_type', 'packages.price as package_price', 'status.name as customer_status', DB::raw('DATE_FORMAT(MAX(rr.receipt_date),"%Y-%m-%d") as rr_date'))
                ->groupBy('customers.id')
                ->get();
            $current_bill = DB::table('bills')->where('id', '=', $request->bill_id)->first();

            $smsgateway = SmsGateway::first();
            $billings->appends($request->all())->links();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'max_receipt' => $max_receipt,
                'prepaid_customers' => $prepaid_customers,
                'receivable' => $receivable,
                'paid' => $paid,
                'current_bill' => $current_bill,
                'last_invoices' => $last_invoices,
          
                'package_type' => $package_type,
                'smsgateway' => $smsgateway,
            ]);
        } else {

            $lists = Bills::all();
            $packages =  Package::orderBy('price', 'ASC')->get();
         
            $package_type =  Package::select('type')
                ->groupBy('type')
                ->orderBy('type', 'ASC')->get();
            $townships = Township::get();
            $status = Status::get();
            $users = User::orderBy('name', 'ASC')->get();
            return Inertia::render('Client/BillList', [
                'lists' => $lists,
                'packages' => $packages,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
          
                'package_type' => $package_type,
            ]);
        }
    }
    public function makeSinglePDF(Request $request)
    {

        $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();
        $options = [
            'format' => 'A4',
            'default_font_size' => '11',
            'orientation'   => 'P',
            'encoding'      => 'UTF-8',
            'margin_top'  => 45,
            'title' => $invoice->ftth_id,
        ];
        $name = date("ymdHis") . '-' . $invoice->bill_number . ".pdf";
        $path = $invoice->ftth_id . '/' . $name;
        $pdf = $this->createPDF($options, 'invoice', $invoice, $name, $path);
        $invoice_no = "INV" . substr($invoice->bill_number, 0, 4) . str_pad($invoice->invoice_number, 5, "0", STR_PAD_LEFT);
        if ($pdf['status'] == 'success') {

            // Successfully stored. Return the full path.
            $invoice->invoice_file =  $pdf['disk_path'];
            $invoice->invoice_url = $pdf['shortURL'];
            $invoice->update();
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($invoice)
                ->log('Create Single PDF for' . $invoice->ftth_id . ', Invoice ID : ' .  $invoice_no);
            return redirect()->back()->with('message', 'PDF Generated Successfully.');
        }

        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($invoice)
            ->log('Single PDF Creation Failed for ' . $invoice->ftth_id . ', Invoice ID : ' .  $invoice_no);
        // download PDF file with download method
        return redirect()->back()->with('message', 'PDF Generation Fails.');
    }



    public function makeAllPDF(Request $request)
    {
        // dd($request);
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->where('invoices.total_payable', '>', 0)
            ->where('bill_id', '=', $request->bill_id)
            ->whereNull('invoice_file')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->select('invoices.*')
            ->get();
        if ($invoices) {

            foreach ($invoices as $invoice) {

                //event(new PdfGenerationProgress($count++));
                dispatch(new PDFCreateJob($invoice->id));
            }
            // download PDF file with download method
            return redirect()->back()->with('message', 'PDF Generation is running background.');
        }
    }
    public function sendSingleEmail(Request $request)
    {
        if ($request->id) {
            $invoice = Invoice::find($request->id);



            if ($invoice->email) {

                $email_template = EmailTemplate::where('default', '=', 1)
                    ->where('type', '=', 'email')
                    ->first();
                if ($email_template) {

                    $billing_email = $invoice->email;
                    // $data["email"] = $invoices->email;
                    $data["email"] = $billing_email;
                    if (strpos($billing_email, ',') !== false) {
                        $data["email"] = explode(",", $billing_email);
                    }
                    if (strpos($billing_email, ';') !== false) {
                        $data["email"] = explode(';', $billing_email);
                    }
                    if (strpos($billing_email, ':') !== false) {
                        $data["email"] = explode(':', $billing_email);
                    }
                    if (strpos($billing_email, ' ') !== false) {
                        $data["email"] = explode(' ', $billing_email);
                    }
                    if (strpos($billing_email, '/') !== false) {
                        $data["email"] = explode('/', $billing_email);
                    }

                    $email_title = $email_template->title;
                    $email_body = $email_template->body;
                    $email_title = $this->replaceMarkup($email_title, $request->id);
                    $email_body = $this->replaceMarkup($email_body, $request->id);

                    $cc_emails = $email_template->cc_email;
                    if (strpos($cc_emails, ','))
                        $cc_emails = explode(",", $cc_emails);
                    $data["cc"] = $cc_emails;
                    $data["title"] = $email_title;
                    $data["body"] = $email_body;
                    $attachment =  $invoice->file;
                    Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                        $message->to($data["email"], $data["email"])
                            ->cc($data["cc"])
                            ->subject($data["title"]);
                        if ($attachment) {
                            $message->attach($attachment);
                        }
                    });

                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    if (Mail::failures()) {
                        $billing_data->mail_sent_status = "error";
                    } else {
                        $billing_data->mail_sent_status = "sent";
                    }
                    $billing_data->update();

                    if (Mail::failures()) {
                        return redirect()->back()->with('message', 'Email Cannot Send');
                    } else {
                        return redirect()->back()->with('message', 'Sent Email Successfully.');
                    }
                }
            }
        } else {
            return redirect()->back()->with('message', 'Customer does not have email address !');
        }
    }

    public function sendAllEmail(Request $request)
    {
        ini_set('max_execution_time', '0');
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
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
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->total_payable_min, function ($query, $total_payable_min) {
                $query->where('invoices.total_payable', '>=', $total_payable_min);
            })
            ->when($request->total_payable_max, function ($query, $total_payable_max) {
                $query->where('invoices.total_payable', '<=', $total_payable_max);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })

            ->when($request->township, function ($query, $township) {
                $query->where('customers.township_id', '=', $township);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->select('invoices.*')
            ->get();
        $email_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'email')
            ->first();
        foreach ($invoices as $invoice) {
            if ($invoice->email && $invoice->mail_sent_status != 'sent') {

                $billing_email =  preg_replace('/\s+/', '', $invoice->email); //remove space from email

                $data["email"] = $billing_email;
                if (strpos($billing_email, ',') !== false) {
                    $data["email"] = explode(",", $billing_email);
                }
                if (strpos($billing_email, ';') !== false) {
                    $data["email"] = explode(';', $billing_email);
                }
                if (strpos($billing_email, ':') !== false) {
                    $data["email"] = explode(':', $billing_email);
                }
                if (strpos($billing_email, ' ') !== false) {
                    $data["email"] = explode(' ', $billing_email);
                }
                if (strpos($billing_email, '/') !== false) {
                    $data["email"] = explode('/', $billing_email);
                }
                // $data["email"] = 'kkhinehtoo@gmail.com';
                $email_title = $email_template->title;
                $email_body = $email_template->body;
                $email_title = $this->replaceMarkup($email_title, $request->id);
                $email_body = $this->replaceMarkup($email_body, $request->id);
                $cc_emails = $email_template->cc_email;
                if (strpos($cc_emails, ','))
                    $cc_emails = explode(",", $cc_emails);

                $data["cc"] = $cc_emails;
                $data["title"] = $email_title;
                $data["body"] = $email_body;
                $attachment =  $invoice->file;
                Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                    $message->to($data["email"], $data["email"])
                        ->cc($data["cc"])
                        ->subject($data["title"]);
                    if ($attachment) {
                        $message->attach($attachment);
                    }
                });

                $biling_data = Invoice::find($invoice->id);

                if (Mail::failures()) {
                    $biling_data->mail_sent_status = "error";
                } else {
                    $biling_data->sent_date = date('j M Y');
                    $biling_data->mail_sent_status = "sent";
                }
                $biling_data->save();
            }
        }
        if (Mail::failures()) {
            return redirect()->back()->with('message', 'Email Cannot Send');
        } else {
            return redirect()->back()->with('message', 'Sent Email Successfully.');
        }
    }

    public function sendSingleSMS(Request $request)
    {

        if ($request->id) {

            $invoice = Invoice::where('invoices.total_payable', '>', 0)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->where('sms_sent_status', '<>', 'sent')
                ->find($request->id);

            if ($invoice->phone && $invoice->sms_sent_status != 'sent') {

                $sms_template = EmailTemplate::whereJsonContains('default_name', ['key' => 'bill_invoice'])
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {

                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    $message = $this->replaceInvoiceMarkup($request->id, $sms_message);
                    $success = $this->deliverSMS($invoice->phone, $message);
                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    $billing_data->sms_sent_status = ($success) ? "sent" : "error";
                    $billing_data->update();
                    if ($success) {
                        return redirect()->back()->with('message', 'Sent SMS Successfully.');
                    } else {
                        return redirect()->back()->with('message', 'SMS Cannot Send');
                    }
                } // end of check sms template
            } // end of check phone exists or not  
            else {
                return redirect()->back()->with('message', 'Customer does not have phone number !');
            }
        } //end of check ID exists or not
    }

    public function sendAllSMS(Request $request)
    {
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
            ->where(function ($query) {
                return $query->where('invoices.sms_sent_status', '<>', 'sent')
                    ->orwherenull('invoices.sms_sent_status');
            })
            ->select('invoices.*')
            ->get();

        foreach ($invoices as $invoice) {
            dispatch(new BillingSMSJob($invoice->id));
        } //end of foreach invoices
        return redirect()->back()->with('message', 'SMS Sending Process Running in Background.');
    }
    public function sendBillReminder(Request $request)
    {
        $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
            ->select('invoice_id')
            ->get()
            ->toArray();
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotIn('invoices.id', $receipts)
            ->where('bill_id', '=', $request->bill_id)
            ->where(function ($query) {
                return $query->where('invoices.reminder_sms_sent_status', '<>', 'sent')
                    ->orwherenull('invoices.reminder_sms_sent_status');
            })
            ->select('invoices.*')
            ->get();

        foreach ($invoices as $invoice) {
            dispatch(new ReminderSMSJob($invoice->id, count($invoices), Auth::id()));
        } //end of foreach invoices
        return redirect()->back()->with('message', 'Bill Reminder Sending Process Running in Background.');
    }
    public function destroyInvoice(Request $request, $id)
    {
        if ($request->has('id')) {
            $invoice = Invoice::find($request->input('id'));
            $receipt = ReceiptRecord::where('invoice_id', '=', $request->input('id'))->first();
            if ($receipt) {
                $receipt_id = $receipt->id;
                ReceiptRecord::find($receipt_id)->delete();
                $months = 12;
                while ($months > 0) {
                    $status =  ReceiptSummery::where($months, '=', $receipt_id)
                        ->where('customer_id', '=', $invoice->customer_id)
                        ->first();
                    if ($status) {
                        $status->$months = null;
                        $status->update();
                    }
                    $months--;
                }
                $receipt->delete();
            }
            $invoice->delete();
            return redirect()->back();
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingTemp::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function destroyall()
    {
        BillingTemp::truncate();
        return redirect()->back();
    }
}
