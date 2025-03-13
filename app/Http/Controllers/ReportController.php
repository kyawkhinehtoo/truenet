<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;
use App\Models\Bills;
use App\Models\Township;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Status;
use App\Models\CustomerHistory;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Role;
use App\Models\ReceiptRecord;
use App\Models\BillAdjustment;
use App\Models\DnPorts;
use App\Models\ReceiptSummery;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Storage;
use Mail;
use DateTime;
use App\Models\SmsGateway;
use App\Models\SnPorts;
use Carbon\Carbon;

class ReportController extends Controller
{
    static $sms_post_url = 'https://api.smsbrix.com/v1/message/send';
    static $sms_status_url = 'https://api.smsbrix.com/v1/message/info/';
    static $sid;
    static $token;
    static $senderid;
    static $header;

    public function __construct()
    {
        $smsgateway = SmsGateway::first();
        if ($smsgateway) {
            if ($smsgateway->status == '1') {
                self::$sid = $smsgateway->sid;
                self::$token = $smsgateway->token;
                self::$senderid = 'MGT eBill';
                self::$header = ['Authorization' => 'Basic ' . base64_encode($smsgateway->sid . ':' . $smsgateway->token)];
            }
        }
    }
    public function incidentReport(Request $request)
    {

        $incidents =  DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('sla', 'packages.sla_id', '=', 'sla.id')
            ->join('users', 'users.id', '=', 'incidents.incharge_id')
            ->when($request->type, function ($query, $type) {
                $query->where('incidents.type', '=', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('incidents.status', '=', $status);
            })
            ->when($request->period, function ($query, $period) {
                
                $query->whereBetween('incidents.date', [$period['0'], $period['1']]);
            }, function ($query) {
                $query->whereDate('incidents.date', Carbon::today());
            })
            ->when($request->general, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->select(
                'incidents.*',
                'customers.ftth_id as ftth_id',
                'users.name as incharge'
            )
            ->paginate(10);
        $incidents->appends($request->all())->links();
        return Inertia::render('Client/IncidentReport', [
            'incidents' => $incidents,


        ]);
    }
    public function getIncidentDetail($id, $date)
    {
        if ($id) {
            $incidents =  DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('sla', 'packages.sla_id', '=', 'sla.id')
                ->join('projects', 'customers.project_id', '=', 'projects.id')
                ->join('users', 'users.id', '=', 'incidents.incharge_id')
                ->where('customers.id', '=', $id)
                ->where('incidents.status', '=', 3)
                ->when($date, function ($query, $date) {
                    $newDate = new DateTime($date);
                    $query->whereMonth('incidents.date', '=',  $newDate->format('m'));
                    $query->whereYear('incidents.date', '=',  $newDate->format('Y'));
                })
                ->select(
                    'incidents.*',
                    'projects.name as project_name',
                    'customers.ftth_id as ftth_id',
                    'users.name as incharge',
                    'sla.percentage as sla',
                    DB::raw("TIMESTAMPDIFF(minute, concat(incidents.date,' ', incidents.time) ,concat(incidents.close_date,' ', incidents.close_time))AS total_minutes ")
                )
                ->get();
        }
        return response()->json($incidents, 200);
    }

    public function showUnpaidBill(Request $request)
    {
        $roles = Role::get();
        $smsgateway = SmsGateway::first();
        $user = User::find(Auth::user()->id);
        if ($request->bill_id) {
            $total_receivable = DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
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
            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $users = User::orderBy('name', 'ASC')->get();

            $orderform = null;
            if ($request->orderform)
                $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

            $max_receipt =  DB::table('invoices')
                ->leftJoin('receipt_records', 'invoices.id', '=', 'receipt_records.invoice_id')
                ->where('invoices.bill_id', '=', $request->bill_id)
                ->select(DB::raw('max(receipt_records.receipt_number) as max_receipt_number'))
                ->first();

            $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
                ->select('invoice_id')
                ->get()
                ->toArray();

            $billings =  DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->whereNotIn('invoices.id', $receipts)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
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
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->total_payable_min, function ($query, $total_payable_min) {
                    $query->where('invoices.total_payable', '>=', $total_payable_min);
                })
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
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
                ->where('invoices.total_payable', '>', 1)
                ->orderBy('customers.id', 'ASC')
                ->select(
                    'invoices.id as id',
                    'invoices.bill_id as bill_id',
                    'invoices.customer_id as customer_id',
                    'status.name as customer_status',
                    'status.type as customer_status_type',
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
                    'invoices.usage_days as usage_days',
                    'invoices.file as file',
                    'invoices.url as url',
                    'invoices.mail_sent_date as sent_date',
                    'invoices.mail_sent_status as status',
                    'invoices.reminder_sms_sent_date as reminder_sms_sent_date',
                    'invoices.reminder_sms_sent_status as reminder_sms_sent_status',
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
                    'invoices.email as email',
                    'invoices.phone as phone'
                )
                ->paginate(10);
            //DATE_FORMAT(date_and_time, '%Y-%m-%dT%H:%i') AS 

            $radius = RadiusController::checkRadiusEnable();

            $billings->appends($request->all())->links();
            $current_bill = DB::table('bills')->where('id', '=', $request->bill_id)->first();
            return Inertia::render('Client/UnpaidReport', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'billings' => $billings,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'max_receipt' => $max_receipt,
                'receivable' => $receivable,
                'paid' => $paid,
                'smsgateway' => $smsgateway,
                'current_bill' => $current_bill,
                'radius' => $radius,
            ]);
        } else {
            $radius = RadiusController::checkRadiusEnable();
            $lists = Bills::all();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $users = User::orderBy('name', 'ASC')->get();
            return Inertia::render('Client/UnpaidReport', [
                'lists' => $lists,
                'packages' => $packages,
                'projects' => $projects,
                'townships' => $townships,
                'status' => $status,
                'users' => $users,
                'user' => $user,
                'roles' => $roles,
                'smsgateway' => $smsgateway,
                'radius' => $radius,
            ]);
        }
    }

    public function contractReport(Request $request)
    {

        $packages = Package::get();

        $customers = DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->when($request->packages, function ($query, $packages) {
                $package_list = array();
                foreach ($packages as $value) {
                    array_push($package_list, $value['id']);
                }
                $query->whereIn('customers.package_id', $package_list);
            })
            ->when($request->month, function ($query, $month) {
                $newDate = new DateTime($month);
                //$query->whereRaw('customers.installation_date + packages.contract_period month = ?', [$newDate->format('m')]);
                //$query->whereMonth(DATE_ADD('customers.installation_date,  INTERVAL  1 DAY'), '=',  $newDate->format('m'));
                $query->whereRaw('YEAR(DATE_ADD(customers.installation_date, INTERVAL  packages.contract_period MONTH)) =?', $newDate->format('Y'));
                $query->whereRaw('MONTH(DATE_ADD(customers.installation_date, INTERVAL  packages.contract_period MONTH)) =?', $newDate->format('m'));
                //$query->whereYear(DB:raw(DATE_ADD('customers.installation_date, INTERVAL  1 DAY')), '=',  $newDate->format('Y'));
            })
            ->when($request->general, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.name', 'LIKE', '%' . $search . '%');
                });
            })
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->orderBy('customers.id')
            ->select('customers.id as id', 'status.name as status_name', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package', 'packages.speed', 'projects.name as project', 'customers.installation_date', 'packages.contract_period', DB::raw('DATE_ADD(customers.installation_date, INTERVAL packages.contract_period MONTH) AS expired'))
            ->paginate(15);
        $customers->appends($request->all())->links();
        return Inertia::render('Client/contractReport', [

            'packages' => $packages,
            'customers' => $customers,

        ]);
    }


    public function terminationReport(Request $request)
    {

        //SELECT c.ftth_id,c.name, c.installation_date, ch.start_date AS termination_date, p.name AS package_name, p.contract_period, s.name AS current_status FROM customers c JOIN status s ON s.id = c.status_id JOIN customer_histories ch ON ch.customer_id = c.id JOIN packages p ON c.package_id = p.id WHERE ch.active = 1 AND s.`type`='terminate' AND TIMESTAMPDIFF(MONTH,c.installation_date,ch.start_date) >= p.contract_period;

        // SELECT c.ftth_id,c.name,  c.installation_date, ch.start_date AS termination_date, p.name AS package_name, p.contract_period, s.name AS current_status FROM customers c JOIN status s ON s.id = c.status_id JOIN incidents ch ON ch.customer_id = c.id JOIN packages p ON c.package_id = p.id  AND s.`type`='terminate' AND TIMESTAMPDIFF(MONTH,c.installation_date,ch.start_date) >= p.contract_period;
        $projects = Project::get();
        $packages = Package::get();
        $status = Status::get();
        if ($request->request_type == 'request') {
            $customers = DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('projects', 'customers.project_id', '=', 'projects.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->leftjoin('incidents', 'incidents.customer_id', 'customers.id')
                //->whereNotNull('incidents.close_date')
                ->where('incidents.status', '=', 1)
                ->where('incidents.type', '=', 'termination')
                ->when($request->condition, function ($query, $condition) {
                    if ($condition == 'full') {
                        $query->whereRaw('TIMESTAMPDIFF(MONTH,customers.installation_date,incidents.start_date) >= packages.contract_period');
                    } else if ($condition == 'under') {
                        $query->whereRaw('TIMESTAMPDIFF(MONTH,customers.installation_date,incidents.start_date) < packages.contract_period');
                    }
                })
                ->when($request->project, function ($query, $projects) {
                    $project_list = array();
                    foreach ($projects as $value) {
                        array_push($project_list, $value['id']);
                    }
                    $query->whereIn('projects.id', $project_list);
                })
                ->when($request->packages, function ($query, $packages) {
                    $package_list = array();
                    foreach ($packages as $value) {
                        array_push($package_list, $value['id']);
                    }
                    $query->whereIn('customers.package_id', $package_list);
                })

                ->when($request->status, function ($query, $status) {
                    $status_list = array();
                    foreach ($status as $value) {
                        array_push($status_list, $value['id']);
                    }
                    $query->whereIn('customers.status_id', $status_list);
                })
                ->when($request->general, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                            ->orWhere('customers.name', 'LIKE', '%' . $search . '%');
                    });
                })
                ->when($request->ter_period, function ($query, $ter_period) {
                    $query->where(function ($query) use ($ter_period) {
                        $query->whereBetween(
                            "incidents.start_date",
                            [
                                $ter_period[0] . " 00:00:00",
                                $ter_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->when($request->req_period, function ($query, $req_period) {
                    $query->where(function ($query) use ($req_period) {
                        $query->whereBetween(
                            "incidents.date",
                            [
                                $req_period[0] . " 00:00:00",
                                $req_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->groupBy('customers.id')
                ->orderBy('customers.id')
                ->select('customers.id as id', 'status.name as status_name', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package', 'packages.speed', 'projects.name as project', 'customers.installation_date', 'packages.contract_period', DB::raw('DATE_ADD(customers.installation_date, INTERVAL packages.contract_period MONTH) AS expired'), 'incidents.start_date as terminated_date')
                ->paginate(15);
            $customers->appends($request->all())->links();
        } else {
            $customers = DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->leftjoin('incidents', 'incidents.customer_id', 'customers.id')
                ->join('projects', 'customers.project_id', '=', 'projects.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_histories', 'customer_histories.customer_id', 'customers.id')
                ->where('status.type', '=', 'terminate')
                ->where('customer_histories.active', '=', 1)
                ->when($request->condition, function ($query, $condition) {
                    if ($condition == 'full') {
                        $query->whereRaw('TIMESTAMPDIFF(MONTH,customers.installation_date,incidents.start_date) >= packages.contract_period');
                    } else if ($condition == 'under') {
                        $query->whereRaw('TIMESTAMPDIFF(MONTH,customers.installation_date,incidents.start_date) < packages.contract_period');
                    }
                })
                ->when($request->project, function ($query, $projects) {
                    $project_list = array();
                    foreach ($projects as $value) {
                        array_push($project_list, $value['id']);
                    }
                    $query->whereIn('projects.id', $project_list);
                })
                ->when($request->status, function ($query, $status) {
                    $status_list = array();
                    foreach ($status as $value) {
                        array_push($status_list, $value['id']);
                    }
                    $query->whereIn('customers.status_id', $status_list);
                })
                ->when($request->packages, function ($query, $packages) {
                    $package_list = array();
                    foreach ($packages as $value) {
                        array_push($package_list, $value['id']);
                    }
                    $query->whereIn('customers.package_id', $package_list);
                })
                ->when($request->general, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                            ->orWhere('customers.name', 'LIKE', '%' . $search . '%');
                    });
                })
                ->when($request->ter_period, function ($query, $ter_period) {
                    $query->where(function ($query) use ($ter_period) {
                        $query->whereBetween(
                            "incidents.start_date",
                            [
                                $ter_period[0] . " 00:00:00",
                                $ter_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->when($request->req_period, function ($query, $req_period) {
                    $query->where(function ($query) use ($req_period) {
                        $query->whereBetween(
                            "incidents.date",
                            [
                                $req_period[0] . " 00:00:00",
                                $req_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->groupBy('customers.id')
                ->orderBy('customers.id')
                ->select('customers.id as id', 'status.name as status_name', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package', 'packages.speed', 'projects.name as project', 'customers.installation_date', 'packages.contract_period', DB::raw('DATE_ADD(customers.installation_date, INTERVAL packages.contract_period MONTH) AS expired'), 'customer_histories.start_date as terminated_date')
                ->paginate(15);
            $customers->appends($request->all())->links();
        }

        return Inertia::render('Client/terminationReport', [
            'projects' => $projects,
            'status' => $status,
            'packages' => $packages,
            'customers' => $customers,

        ]);
    }
    public function suspensionReport(Request $request)
    {

        //SELECT c.ftth_id,c.name, c.installation_date, ch.start_date AS termination_date, p.name AS package_name, p.contract_period, s.name AS current_status FROM customers c JOIN status s ON s.id = c.status_id JOIN customer_histories ch ON ch.customer_id = c.id JOIN packages p ON c.package_id = p.id WHERE ch.active = 1 AND s.`type`='terminate' AND TIMESTAMPDIFF(MONTH,c.installation_date,ch.start_date) >= p.contract_period;

        // SELECT c.ftth_id,c.name,  c.installation_date, ch.start_date AS termination_date, p.name AS package_name, p.contract_period, s.name AS current_status FROM customers c JOIN status s ON s.id = c.status_id JOIN incidents ch ON ch.customer_id = c.id JOIN packages p ON c.package_id = p.id  AND s.`type`='terminate' AND TIMESTAMPDIFF(MONTH,c.installation_date,ch.start_date) >= p.contract_period;
        $projects = Project::get();
        $packages = Package::get();
        $status = Status::get();
        if ($request->request_type == 'request') {
            $customers = DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('projects', 'customers.project_id', '=', 'projects.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('incidents', 'incidents.customer_id', 'customers.id')
                //->whereNotNull('incidents.close_date')
                //->where('incidents.status', '=', 1)
                ->where('incidents.type', '=', 'suspension')
                ->when($request->project, function ($query, $projects) {
                    $project_list = array();
                    foreach ($projects as $value) {
                        array_push($project_list, $value['id']);
                    }
                    $query->whereIn('projects.id', $project_list);
                })
                ->when($request->packages, function ($query, $packages) {
                    $package_list = array();
                    foreach ($packages as $value) {
                        array_push($package_list, $value['id']);
                    }
                    $query->whereIn('customers.package_id', $package_list);
                })

                ->when($request->status, function ($query, $status) {
                    $status_list = array();
                    foreach ($status as $value) {
                        array_push($status_list, $value['id']);
                    }
                    $query->whereIn('customers.status_id', $status_list);
                })
                ->when($request->general, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                            ->orWhere('customers.name', 'LIKE', '%' . $search . '%');
                    });
                })
                ->when($request->sus_period, function ($query, $sus_period) {
                    $query->where(function ($query) use ($sus_period) {
                        $query->whereBetween(
                            "incidents.start_date",
                            [
                                $sus_period[0] . " 00:00:00",
                                $sus_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->when($request->req_period, function ($query, $req_period) {
                    $query->where(function ($query) use ($req_period) {
                        $query->whereBetween(
                            "incidents.date",
                            [
                                $req_period[0] . " 00:00:00",
                                $req_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->groupBy('customers.id')
                ->orderBy('customers.id')
                ->select('customers.id as id', 'status.name as status_name', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package', 'packages.speed', 'projects.name as project', 'customers.installation_date', 'packages.contract_period', 'incidents.start_date as suspension_date', 'incidents.date as request_date')
                ->paginate(15);
            $customers->appends($request->all())->links();
        } else {
            $customers = DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->leftjoin('incidents', 'incidents.customer_id', 'customers.id')
                ->join('projects', 'customers.project_id', '=', 'projects.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_histories', 'customer_histories.customer_id', 'customers.id')
                ->where('status.type', '=', 'suspense')
                ->where('customer_histories.active', '=', 1)
                ->when($request->project, function ($query, $projects) {
                    $project_list = array();
                    foreach ($projects as $value) {
                        array_push($project_list, $value['id']);
                    }
                    $query->whereIn('projects.id', $project_list);
                })
                ->when($request->status, function ($query, $status) {
                    $status_list = array();
                    foreach ($status as $value) {
                        array_push($status_list, $value['id']);
                    }
                    $query->whereIn('customers.status_id', $status_list);
                })
                ->when($request->packages, function ($query, $packages) {
                    $package_list = array();
                    foreach ($packages as $value) {
                        array_push($package_list, $value['id']);
                    }
                    $query->whereIn('customers.package_id', $package_list);
                })
                ->when($request->general, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                            ->orWhere('customers.name', 'LIKE', '%' . $search . '%');
                    });
                })
                ->when($request->sus_period, function ($query, $sus_period) {
                    $query->where(function ($query) use ($sus_period) {
                        $query->whereBetween(
                            "incidents.start_date",
                            [
                                $sus_period[0] . " 00:00:00",
                                $sus_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->when($request->req_period, function ($query, $req_period) {
                    $query->where(function ($query) use ($req_period) {
                        $query->whereBetween(
                            "incidents.date",
                            [
                                $req_period[0] . " 00:00:00",
                                $req_period[1] . " 23:59:59"
                            ]
                        );
                    });
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->groupBy('customers.id')
                ->orderBy('customers.id')
                ->select('customers.id as id', 'status.name as status_name', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.phone_1 as phone', 'packages.name as package', 'packages.speed', 'projects.name as project', 'customers.installation_date', 'packages.contract_period', 'customer_histories.start_date as suspension_date', 'incidents.date as request_date')
                ->paginate(15);
            $customers->appends($request->all())->links();
        }

        return Inertia::render('Client/suspensionReport', [
            'projects' => $projects,
            'status' => $status,
            'packages' => $packages,
            'customers' => $customers,

        ]);
    }


    public function publicIpReport(Request $request)
    {

        $startDate = (isset($request->end_date['0'])) ? $request->end_date['0'] : null;
        $endDate = (isset($request->end_date['1'])) ? $request->end_date['1'] : null;

        $public_ips = DB::table('public_ip_addresses')
            ->join('customers', 'public_ip_addresses.customer_id', '=', 'customers.id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('ip_usage_history', 'public_ip_addresses.id', '=', 'ip_usage_history.ip_id')
            ->when($request->packages, function ($query, $packages) {
                $package_list = array();
                foreach ($packages as $value) {
                    array_push($package_list, $value['id']);
                }
                $query->whereIn('customers.package_id', $package_list);
            })

            ->when(!is_null($startDate) && !is_null($endDate), function ($query) use ($startDate, $endDate) {
                $query->whereBetween('ip_usage_history.end_date', [$startDate, $endDate]);
            })
            ->when($request->general, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('public_ip_addresses.ip_address', 'LIKE', '%' . $search . '%')
                        ->orWhere('public_ip_addresses.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->orderBy('public_ip_addresses.id')
            ->select('public_ip_addresses.id as id', 'customers.id as customer_id', 'customers.name as customer_name', 'customers.ftth_id as ftth_id', 'public_ip_addresses.ip_address as ip_address', 'public_ip_addresses.description as description', 'public_ip_addresses.annual_charge as annual_charge', 'public_ip_addresses.currency as currency', 'packages.name as package_name', 'ip_usage_history.start_date as start_date', 'ip_usage_history.end_date as end_date', 'status.name as status_name')
            ->paginate(15);
        $public_ips->appends($request->all())->links();
        return Inertia::render('Client/PublicIpReport', [

            'public_ips' => $public_ips,

        ]);
    }


    public function dnSNReport(Request $request)
    {
        $min = $request->customer_min;
        $max = $request->customer_max;
        $dns = DnPorts::get();
        $sns = DB::table('sn_ports')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            ->when($request->general, function ($query, $keyword) {
                $query->where('sn_ports.name', 'LIKE', '%' . $keyword . '%');
                $query->orwhere('sn_ports.description', 'LIKE', '%' . $keyword . '%');
            })

            ->select('sn_ports.*', 'dn_ports.name as dn_name')
            ->paginate(10);
        $sns_all = SnPorts::get();
        $overall = DB::table('sn_ports')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            ->leftjoin('customers', 'sn_ports.id', '=', 'customers.sn_id')
            ->select('sn_ports.id', 'sn_ports.name', 'sn_ports.description', 'sn_ports.dn_id', 'sn_ports.location', 'sn_ports.input_dbm', 'dn_ports.name as dn_name', DB::raw('count(customers.id) as ports'))
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('sn_ports.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('sn_ports.description', 'LIKE', '%' . $general . '%')
                        ->orWhere('dn_ports.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('dn_ports.description', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->dn, function ($query, $dn_2) {
                $query->where('dn_ports.id', '=', $dn_2);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('sn_ports.id', '=', $sn);
            })
            ->when($min !== null, function ($query) use ($min) {
                $query->havingRaw('count(customers.id) >= ?', [$min]);
            })
            ->when($max !== null, function ($query) use ($max) {
                $query->havingRaw('count(customers.id) <= ?', [$max]);
            })
            // Add this line for filtering
            ->groupBy(['sn_ports.id', 'sn_ports.dn_id', 'sn_ports.name'])
            ->paginate(20);
        $overall->appends($request->all())->links();
        return Inertia::render(
            'Client/SNPorts',
            ['sns' => $sns, 'dns' => $dns, 'overall' => $overall, 'sns_all' => $sns_all]
        );
    }







    // intentionally left spaces
    public function sendSingleSMS(Request $request)
    {
        $smsgateway = SmsGateway::first();
        if ($request->id && $smsgateway->status == '1') {
            $orginal_invoice = Invoice::select('invoices.id as invoice_id', 'invoices.*')->whereNotNull('invoices.file')->find($request->id);
            $last_adjustment = BillAdjustment::where('invoice_id', '=', $request->id)->latest('id')->first();
            $invoice = ($last_adjustment) ? $last_adjustment : $orginal_invoice;

            if ($invoice->phone && $invoice->reminder_sms_sent_status != 'sent') {

                $sms_template = EmailTemplate::where('name', '=', 'Bill Reminder')
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {
                    $billing_phone = $invoice->phone;
                    $phones = $billing_phone;
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
                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    if (is_array($phones)) {
                        foreach ($phones as $phone) {
                            $pattern = "/^(959)+[0-9]+$/";
                            if (preg_match($pattern, $phone)) {
                                //$phone = '09'.$phone;
                                $phone = preg_split("/^959/", $phone);
                                // $phone = $phone[1];
                                $phone = '9' . $phone[1];
                            }
                            $email_body = $this->replaceMarkup($sms_message, $request->id);
                            $sms_response = $this->sendSMS($phone, $email_body);
                            if ($sms_response['status'] == 'success') {
                                $client = new \GuzzleHttp\Client();
                                $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                                $statusresponseBody = json_decode($status_response->getBody(), true);
                                if ($statusresponseBody['status'] == 'Sent') {
                                    $success = true;
                                }
                            }
                        }
                    } else {
                        $pattern = "/^(959|\+959)+[0-9]+$/";
                        if (preg_match($pattern, $phones)) {
                            //$phone = '09'.$phone;
                            $phones = preg_split("/(^\+959|959)/", $phones);
                            // $phones = $phones[1];
                            $phones = '9' . $phones[1];
                        }
                        $email_body = $this->replaceMarkup($sms_message, $request->id);
                        $sms_response = $this->sendSMS($phones, $email_body);
                        if ($sms_response['status'] == 'success') {
                            $client = new \GuzzleHttp\Client();
                            $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                            $statusresponseBody = json_decode($status_response->getBody(), true);
                            if ($statusresponseBody['status'] == 'Sent') {
                                $success = true;
                            }
                        }
                    }
                    // $billing_data = Invoice::find($invoice->id);
                    $invoice->reminder_sms_sent_date = date('j M Y');
                    $invoice->reminder_sms_sent_status = ($success) ? "sent" : "error";
                    $invoice->update();
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
        $smsgateway = SmsGateway::first();
        if ($smsgateway->status == '1') {
            ini_set('max_execution_time', '0');
            $receipts = ReceiptRecord::where('bill_id', '=', $request->id)
                ->select('invoice_id')
                ->get()
                ->toArray();
            $invoices =  DB::table('invoices')
                ->join('customers', 'customers.id', '=', 'invoices.customer_id')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->whereNotIn('invoices.id', $receipts)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->where('invoices.bill_id', '=', $request->id)
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
                            ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->total_payable_min, function ($query, $total_payable_min) {
                    $query->where('invoices.total_payable', '>=', $total_payable_min);
                })
                ->when($request->total_payable_max, function ($query, $total_payable_max) {
                    $query->where('invoices.total_payable', '<=', $total_payable_max);
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
                ->when($request->order, function ($query, $order) {
                    $query->whereBetween('customers.order_date', $order);
                })
                ->when($request->installation, function ($query, $installation) {
                    $query->whereBetween('customers.installation_date', $installation);
                })
                ->where('invoices.total_payable', '>', 1)
                ->orderBy('customers.id', 'ASC')
                ->select(
                    'invoices.id as id',
                    'invoices.bill_id as bill_id',
                    'invoices.customer_id as customer_id',
                    'status.name as customer_status',
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
                    'invoices.usage_days as usage_days',
                    'invoices.file as file',
                    'invoices.url as url',
                    'invoices.mail_sent_date as sent_date',
                    'invoices.mail_sent_status as status',
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
                    'invoices.email as email',
                    'invoices.phone as phone'
                )
                ->get();
            $sms_template = EmailTemplate::where('name', '=', 'Bill Reminder')
                ->where('type', '=', 'sms')
                ->first();
            foreach ($invoices as $my_invoice) {
                $orginal_invoice = Invoice::select('invoices.id as invoice_id', 'invoices.*')->whereNotNull('invoices.file')->find($my_invoice->id);
                $last_adjustment = BillAdjustment::where('invoice_id', '=', $my_invoice->id)->latest('id')->first();

                $last_adjustment = BillAdjustment::where('invoice_id', '=', $my_invoice->id)->latest('id')->first();
                $invoice = ($last_adjustment) ? $last_adjustment : $orginal_invoice;

                if ($invoice->phone && $invoice->reminder_sms_sent_status != 'sent') {

                    $billing_phone = $invoice->phone;

                    if ($sms_template) {
                        $billing_phone = $invoice->phone;
                        $phones = $billing_phone;
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
                        $sms_message = $sms_template->body;
                        $sms_response = null;
                        $success = false;
                        if (is_array($phones)) {
                            foreach ($phones as $phone) {
                                $pattern = "/^(959|\+959)+[0-9]+$/";
                                if (preg_match($pattern, $phone)) {
                                    //$phone = '09'.$phone;
                                    $phone = preg_split("/(^\+959|959)/", $phone);
                                    //  $phone = $phone[1];
                                    $phone = '9' . $phone[1];
                                }
                                $email_body = $this->replaceMarkup($sms_message, $invoice->id);
                                $sms_response = $this->sendSMS($phone, $email_body);
                                if ($sms_response['status'] == 'success') {
                                    $client = new \GuzzleHttp\Client();
                                    $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                                    $statusresponseBody = json_decode($status_response->getBody(), true);
                                    if ($statusresponseBody['status'] == 'Sent') {
                                        $success = true;
                                    }
                                }
                            }
                        } else {
                            $pattern = "/^(959|\+959)+[0-9]+$/";
                            if (preg_match($pattern, $phones)) {
                                //$phone = '09'.$phone;
                                $phones = preg_split("/(^\+959|959)/", $phones);
                                // $phones = $phones[1];
                                $phones = '9' . $phones[1];
                            }
                            $email_body = $this->replaceMarkup($sms_message, $invoice->id);

                            $sms_response = $this->sendSMS($phones, $email_body);
                            if ($sms_response['status'] == 'success') {
                                $client = new \GuzzleHttp\Client();
                                $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
                                $statusresponseBody = json_decode($status_response->getBody(), true);
                                if ($statusresponseBody['status'] == 'Sent') {
                                    $success = true;
                                }
                            }
                        }
                        // $billing_data = Invoice::find($invoice->id);
                        $invoice->reminder_sms_sent_date = date('j M Y');
                        $invoice->reminder_sms_sent_status = ($success) ? "sent" : "error";
                        $invoice->update();
                    } // end of check sms template
                } // end of check phone exists or not  
            } //end of foreach invoices
        }
        return redirect()->back()->with('message', 'Sent SMS Successfully.');
    }

    public function sendSMS($phone, $message)
    {
        $postInput  =  [
            'senderid' => self::$senderid,
            'number' => trim($phone),
            'message' => trim($message),
        ];
        //$response = Http::withHeaders($header)->post(self::$sms_post_url,$postInput );
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', self::$sms_post_url, ['form_params' => $postInput, 'headers' => self::$header]);
        $responseBody = json_decode($response->getBody(), true);
        sleep(2);
        return $responseBody;
    }

    public function replaceMarkup($data, $id)
    {
        $invoice = Invoice::find($id);
        if ($invoice) {
            $invoice_num = 'INV' . str_pad($invoice->invoice_number, 5, "0", STR_PAD_LEFT) . substr($invoice->bill_number, 0, 4);
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = 'https://oss.marga.com.mm/s/' . $invoice->url;
            $search = array('{{ftth_id}}', '{{bill_number}}', '{{invoice_number}}', '{{period_covered}}', '{{month}}', '{{year}}', '{{bill_to}}', '{{attn}}', '{{usage_days}}', '{{total_payable}}', '{{payment_duedate}}', '{{url}}');
            $replace = array(substr($invoice->ftth_id, 0, 5), $invoice->bill_number, $invoice_num, $invoice->period_covered, $month, $invoice->bill_year, $invoice->bill_to, $invoice->attn, $invoice->usage_days, $invoice->total_payable, $invoice->payment_duedate, $bill_url);
            $replaced = str_replace($search, $replace, $data);
            return $replaced;
        }
    }
}
