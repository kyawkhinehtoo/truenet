<?php

namespace App\Http\Controllers;

use App\Models\BillingConfig;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Bills;
use App\Models\Customer;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Traits\PdfTrait;

class ReceiptController extends Controller
{
    use PdfTrait;
    public function index(Request $request)
    {
        return $this->show($request);
    }
    public function show(Request $request)
    {
        $records = ReceiptRecord::when($request->sh_year, function ($query, $sh_year) {
            $query->where('receipt_records.year', '=', $sh_year);
        }, function ($query) {
            $query->where('receipt_records.year', '=', date("Y"));
        })->get();
        $bills = Bills::get();
        $receipt_summeries = Customer::join('receipt_summery', 'receipt_summery.customer_id', '=', 'customers.id')
            ->when($request->sh_general, function ($query, $sh_general) {
                $query->where(function ($query) use ($sh_general) {
                    $query->where('customers.name', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $sh_general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $sh_general . '%');
                });
            })
            ->when($request->sh_year, function ($query, $sh_year) {
                $query->where('receipt_summery.year', '=', $sh_year);
            }, function ($query) {
                $query->where('receipt_summery.year', '=', date("Y"));
            })
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })

            ->orderBy('customers.ftth_id')
            // ->select('receipt_summery.*','customers.ftth_id as ftth_id')
            ->paginate(15);
        //   $this->mapRecords($receipt_summeries, $records);
        $receipt_summeries->appends($request->all())->links();
        return Inertia::render('Client/ReceiptSummery', [
            'receipt_summeries' => $receipt_summeries,
            'records' => $records,
            'bills' => $bills,
        ]);
    }
    public function mapRecords($rss, $rrs)
    {

        foreach ($rss as $key => $rs) {
            for ($i = 1; $i <= 12; $i++) {
                foreach ($rrs as $rr) {
                    if ($rs[$i]) {

                        if ($rr->invoice_id === $rs[$i]) {

                            $rs[$i] = $rr;
                        }
                    }
                }
            }

            $rss->data[$key] = $rs;
        }
    }

    public function store(Request $request)
    {



        Validator::make($request->all(), [
            'id' => 'required|max:255', //invoice_id
            'bill_id' => 'required|max:255', //bill_id
            'bill_number' => 'required|max:255',
            'customer_id' => 'required|max:255',
            'type' => 'required', //cash or kbz pay etc..
            'currency' => 'required|max:255',
            'extra_amount' => 'required|max:255',
            'bill_month' => 'required', // month
            'bill_year' => 'required', // year
            'receipt_date' => 'required', // year
            'collected_amount' => 'required', // year

        ])->validate();
        if ($request) {
            $max_receipt_id =  DB::table('receipt_records')
                ->where('receipt_records.bill_id', '=', $request->bill_id)
                ->select(DB::raw('max(CONVERT(receipt_records.receipt_number,UNSIGNED INTEGER)) as max_receipt_number'))
                ->first();

            $receipt = new ReceiptRecord();
            $receipt->customer_id = $request->customer_id;
            $receipt->receipt_number = ($max_receipt_id) ? ($max_receipt_id->max_receipt_number + 1) : 1;
            $receipt->bill_id = $request->bill_id;
            $receipt->bill_no = $request->bill_number;
            $receipt->invoice_id = $request->id;
            $receipt->month = $request->bill_month;
            $receipt->year = $request->bill_year;
            $receipt->transition = $request->transition;
            $receipt->receipt_person = Auth::user()->id;
            $receipt->payment_channel = ($request->type) ? implode(', ', $request->type) : null;
            $receipt->collected_currency = $request->currency;

            $receipt->collected_amount = $request->collected_amount;
            if ($request->user)
                $receipt->collected_person = $request->user['id'];
            $receipt->receipt_date = $request->receipt_date;

            $receipt->issue_amount = $request->total_payable;
            $receipt->remark = $request->remark;

            if ($request->collected_amount == 0) {
                $receipt->status = "outstanding";
            } else {
                //'outstanding','full_paid','over_paid','under_paid','no_invoice'
                if ($request->total_payable == $request->collected_amount)
                    $receipt->status = "full_paid";
                if ($request->total_payable < $request->collected_amount)
                    $receipt->status = "over_paid";
                if ($request->total_payable > $request->collected_amount)
                    $receipt->status = "under_paid";
            }
            $receipt->save();
            $this->ReceiptPaid($receipt->id, $request->customer_id);
            //$this->updateRRS($request->id, $request->customer_id, $request->bill_month, $request->bill_year);
            //$this->runReceiptSummery();

            $changes = $receipt->getChanges();    // Get the updated values after the update

            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'to' => $newValue                   // New value
                ];
            }
            $invoice = Invoice::find($receipt->invoice_id);
            $invoice_no = "INV" . substr($invoice->bill_number, 0, 4) . str_pad($invoice->invoice_number, 5, "0", STR_PAD_LEFT);

            activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($receipt)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Bill Receipt. Customer ID: ' . $invoice->ftth_id . ', Invoice No. : ' . $invoice_no);
        }

        // {"id":2,"customer_id":5,"period_covered":"2021-10-01 to 2021-10-31","bill_number":"2110-A0006-FTTH","ftth_id":"A0006-190425-TCL-FTTH","date_issued":"2021-11-09","bill_to":"Sar Pay Law Ka","attn":"Shop 4, The Central Boulevard, Kabar Aye Pagoda Road, Yangon","previous_balance":"0","current_charge":"46900","compensation":"0","otc":"0","sub_total":"46900","payment_duedate":"2021-11-16","service_description":"Business Fiber","qty":"10 Mbps","usage_days":"1 Month","normal_cost":"46900","total_payable":"46900","discount":"0","email":null,"phone":"959515313","bill_year":"2021","bill_month":"10","device_rental_amount":null,"device_rental_price":null,"device_rental_qty":0,"product_id_amount":null,"product_id_price":null,"product_id_qty":0,"foc_amount":null,"foc_price":null,"foc_qty":0,"setup_fees_amount":null,"setup_fees_price":null,"setup_fees_qty":0,"lan_amount":null,"lan_price":null,"lan_qty":0,"device_amount":null,"device_price":null,"device_name_qty":0,"commercial_tax":5,"final_payment":null,"amount_in_word":"Amount in words: Forty-six Thousand Nine Hundred Kyats Only","user":null,"type":"cash","currency":"mmk","collected_amount":"46900","extra_amount":0,"customer_status":"Suspend"}
        return redirect()->back()->with('message', 'Receipt Made Successfully.');
    }
    // public function setExpiry($receipt_id, $customer_id){
    //     $rr_invoice = ReceiptRecord::join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
    //         ->where('receipt_records.id','=',$receipt_id)
    //         ->where('receipt_records.customer_id','=',$customer_id)
    //         ->select('receipt_records.*', 'invoices.period_covered as period_covered')
    //         ->first();
    //     $rr_adjust = ReceiptRecord::join('bill_adjustment', 'bill_adjustment.invoice_id', '=', 'receipt_records.invoice_id')
    //         ->where('receipt_records.id','=',$receipt_id)
    //         ->where('receipt_records.customer_id','=',$customer_id)
    //         ->select('receipt_records.*', 'bill_adjustment.period_covered as period_covered')
    //         ->latest('id')
    //         ->first();
    //     $receipt_record = ($rr_adjust)?$rr_adjust:$rr_invoice;
    //     if ($receipt_record) {



    //         if ($receipt_record->period_covered) {
    //             if (strpos($receipt_record->period_covered, ' to ')) {
    //                 $p_months = explode(" to ", $receipt_record->period_covered);

    //                 $from = (new DateTime($p_months[0]))->modify('first day of this month');

    //                 $to = (new DateTime($p_months[1]))->modify('first day of next month');
    //                 $interval = DateInterval::createFromDateString('1 month');
    //                 $period   = new DatePeriod($from, $interval, $to);
    //                 foreach ($period as $dt) {

    //                     $this->updateRRS($receipt_record->id, $receipt_record->customer_id, $dt->format("n"), $dt->format("Y"));
    //                 }
    //             }
    //         }

    // }


    // }
    public function template(Request $request)
    {
        $receipt = ReceiptRecord::where('receipt_records.id', '=', $request->id)
            ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('invoices.*', 'receipt_records.*', DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date_2'))
            ->first();

        return view('receipt_template', $receipt);
    }
    // public function makeReceiptPDF(Request $request)
    // {
    //     $receipt = ReceiptRecord::where('receipt_records.id', '=', $request->id)
    //         ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
    //         ->select('invoices.*', 'receipt_records.*', DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date_2'))
    //         ->first();

    //     $options = [
    //         'enable-local-file-access' => true,
    //         'orientation'   => 'portrait',
    //         'encoding'      => 'UTF-8',
    //         'footer-spacing' => 5,
    //         'header-spacing' => 5,
    //         'margin-top'  => 20,
    //         'footer-html'   => view('footer')
    //     ];

    //     view()->share('receipt_template', $receipt);
    //     $pdf = PDF::loadView('receipt_template', $receipt);

    //     $pdf->setOptions($options);
    //     $output = $pdf->output();
    //     $receipt_num = 'R' . str_pad($receipt->receipt_number, 5, "0", STR_PAD_LEFT) . '-' . substr($receipt->bill_number, 0, 4) . '-' . substr($receipt->ftth_id, 0, 5);
    //     $name = $receipt_num . ".pdf";
    //     $disk = Storage::disk('public');

    //     if ($disk->put('bill_receipt/' . $receipt->year . '/' . $receipt->month . '/' . $receipt->ftth_id . '/' . $name, $output)) {
    //         // Successfully stored. Return the full path.
    //         $receipt->file =  $disk->path('bill_receipt/' . $receipt->year . '/' . $receipt->month . '/' . $receipt->ftth_id . '/' . $name);

    //         $receipt->update();
    //     }

    //     // download PDF file with download method
    //     return redirect()->back()->with('message', 'PDF Generated Successfully.');
    // }
    public function makeReceiptPDF(Request $request)
    {

        // $receipt =  ReceiptRecord::join('invoices', 'receipt_records.invoice_id', '=', 'invoices.id')
        // ->leftjoin('users', 'users.id', '=', 'receipt_records.receipt_person')
        // ->join('customers', 'receipt_records.customer_id', 'customers.id')
        // ->join('packages', 'customers.package_id', 'packages.id')
        // ->where('receipt_records.id', '=', $request->id)
        // ->select('invoices.*', 'packages.type as service_type','receipt_records.id', 'receipt_records.remark as remark', 'receipt_records.collected_amount as collected_amount', 'receipt_records.receipt_date as receipt_date', 'receipt_records.receipt_number as receipt_number','receipt_records.receipt_file','receipt_records.receipt_url' ,'users.name as collector')
        // ->first();
        $receipt = ReceiptRecord::where('receipt_records.id', '=', $request->id)
            ->join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('invoices.*', 'receipt_records.*', DB::raw('DATE_FORMAT(receipt_records.receipt_date,"%Y-%m-%d") as receipt_date_2'))
            ->first();
        $options = [
            'default_font_size' => '11',
            'orientation'   => 'P',
            'encoding'      => 'UTF-8',
            'margin_top'  => 45,
            'margin_bottom'  => 1,
            'title' => $receipt->ftth_id,
        ];

        // dd($invoice);

        $name = date("ymdHis") . '-R' . $receipt->bill_number . ".pdf";
        $path = $receipt->ftth_id . '/' . $name;
        $pdf = $this->createPDF($options, 'receipt', $receipt, $name, $path);
        if ($pdf['status'] == 'success') {
            // Successfully stored. Return the full path.
            $receipt->receipt_file =  $pdf['disk_path'];
            $receipt->receipt_url = $pdf['shortURL'];
            $receipt->update();
        }


        // download PDF file with download method
        return redirect()->back()->with('message', 'Receipt PDF Generated Successfully.');
    }
    public function ReceiptPaid($receipt_id, $customer_id)
    {
        $rr_invoice = ReceiptRecord::join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->join('customers', 'customers.id', 'receipt_records.customer_id')
            ->where('receipt_records.id', '=', $receipt_id)
            ->where('receipt_records.customer_id', '=', $customer_id)
            ->select('receipt_records.*', 'invoices.start_date', 'invoices.end_date', 'invoices.ftth_id as ftth_id', 'customers.customer_type as customer_type')
            ->first();

        $receipt_record = $rr_invoice;
        if ($receipt_record) {
            if ($receipt_record->start_date && $receipt_record->end_date) {
                //to get exact end date to use in radius
                $bill_from = new DateTime($receipt_record->start_date);
                $bill_to = new DateTime($receipt_record->end_date);

                $from = (new DateTime($receipt_record->start_date))->modify('first day of this month');
                $to = (new DateTime($receipt_record->end_date))->modify('first day of next month');
                $interval = DateInterval::createFromDateString('1 month');
                $period   = new DatePeriod($from, $interval, $to);

                foreach ($period as $dt) {

                    $this->updateRRS($receipt_record->id, $receipt_record->customer_id, $dt->format("n"), $dt->format("Y"));
                }
                RadiusController::setExpiry($receipt_record->ftth_id, $bill_to->format('Y-m-d 23:59:00'));
            }
        }
    }
    public function runReceiptSummery()
    {
        ini_set('max_execution_time', '0');
        $rr_invoices = ReceiptRecord::join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('receipt_records.*', 'invoices.period_covered as period_covered')
            ->get();

        $receipt_records = ReceiptRecord::join('invoices', 'invoices.id', '=', 'receipt_records.invoice_id')
            ->select('receipt_records.*', 'invoices.period_covered as period_covered')->get();
        if ($rr_invoices) {
            //check modified invoice

            foreach ($rr_invoices as $rr_invoice) {

                $receipt_record = $rr_invoice;
                if ($receipt_record->start_date && $receipt_record->end_date) {



                    $from = (new DateTime($receipt_record->start_date))->modify('first day of this month');

                    $to = (new DateTime($receipt_record->end_date))->modify('first day of next month');
                    $interval = DateInterval::createFromDateString('1 month');
                    $period   = new DatePeriod($from, $interval, $to);
                    foreach ($period as $dt) {

                        $this->updateRRS($receipt_record->id, $receipt_record->customer_id, $dt->format("n"), $dt->format("Y"));
                    }
                }
            }
        }
    }
    public function updateRRS($receipt_id, $customer_id, $month, $year)
    {
        $rs = ReceiptSummery::where('customer_id', '=', $customer_id)
            ->where('year', '=', $year)
            ->first();
        if ($rs) {
            $rs->$month = $receipt_id;
            $rs->year = $year;
            $rs->update();
        } else {
            $receipt_summery = new ReceiptSummery();
            $receipt_summery->$month = $receipt_id;
            $receipt_summery->year = $year;
            $receipt_summery->customer_id = $customer_id;
            $receipt_summery->save();
        }
    }
}
