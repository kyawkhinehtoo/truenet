<?php

namespace App\Http\Controllers;

use App\Jobs\AnnouncementSMSJob;
use Illuminate\Http\Request;
use App\Models\Township;
use App\Models\Project;
use App\Models\Customer;
use App\Models\Package;
use App\Models\EmailTemplate;
use App\Models\Status;
use App\Models\Announcement;
use App\Models\DiscountCoupon;
use App\Models\AnnouncementLog;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use DateTime;
use App\Models\SmsGateway;

class AnnouncementController extends Controller
{
    //
    static $sms_post_url = 'https://api.smsbrix.com/v1/message/send';
    static $sms_status_url = 'https://api.smsbrix.com/v1/message/info/';
    static $sid;
    static $token;
    static $senderid;
    static $header;

    public function index(Request $request)
    {
        $announcements = Announcement::get();
        $templates = EmailTemplate::whereJsonContains('default_name', ['key' => 'general'])->get();
        //  $sms_template = EmailTemplate::whereJsonContains('default_name', 'key', 'bill_invoice')
        $packages = Package::orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();
        $package_except = $request->package_except;
        $township_except = $request->township_except;
        $status_except = $request->status_except;

        $package_speed = Package::select('speed', 'type')
            ->groupBy('speed', 'type')
            ->orderBy('speed', 'ASC')->get();
        $customers =  DB::table('customers')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->package, function ($query, $packages) use ($package_except) {

                $query->where(function ($query) use ($packages, $package_except) {
                    foreach ($packages as $package) {
                        if ($package_except) {
                            $query->orwhere(function ($query) use ($package) {
                                $query->where('packages.speed', '<>', $package['speed'])->where('packages.type', '<>', $package['type']);
                            });
                        } else {

                            $query->orwhere(function ($query) use ($package) {
                                $query->where('packages.speed', '=', $package['speed'])->where('packages.type', '=', $package['type']);
                            });
                        }
                    }
                });
            })
            ->when($request->partner, function ($query, $project) {
                $query->where('customers.project_id', '=', $project);
            })
            ->when($request->township, function ($query, $townships) use ($township_except) {
                $township_list = array();
                foreach ($townships as $value) {
                    array_push($township_list, $value['id']);
                }
                if ($township_except) {
                    $query->whereNotIn('customers.township_id', $township_list);
                } else {
                    $query->whereIn('customers.township_id', $township_list);
                }
            })
            ->when($request->status, function ($query, $status) use ($status_except) {

                $status_list = array();
                foreach ($status as $value) {
                    array_push($status_list, $value['id']);
                }
                if ($status_except) {
                    $query->whereNotIn('customers.status_id', $status_list);
                } else {
                    $query->whereIn('customers.status_id', $status_list);
                }
            })
            ->when($request->status_type, function ($query, $status_type) {
                $query->where('status.type', '=', $status_type);
            })
            ->when($request->payment, function ($query, $payment) {
                if ($payment == 1) {
                    $query->where('customers.payment_type', '=', '1');
                } else {
                    $query->where('customers.payment_type', '=', '0');
                }
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->when($request->deposit_status, function ($query, $deposit_status) {
                $query->where('customers.deposit_status', '=', $deposit_status);
            })
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort == 'cid') {
                    $sort_by = 'customers.id';
                } elseif ($sort == 'cname') {
                    $sort_by = 'customers.name';
                } elseif ($sort == 'township') {
                    $sort_by = 'townships.name';
                } elseif ($sort == 'package') {
                    $sort_by = 'packages.name';
                } elseif ($sort == 'order') {
                    $sort_by = 'customers.order_date';
                }

                $query->orderBy($sort_by, 'desc');
            }, function ($query) {
                $query->orderBy('customers.ftth_id', 'desc');
            })
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color')
            ->paginate(15);
        // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Announcement', [
            'packages' => $packages,
            'projects' => $projects,
            'townships' => $townships,
            'status' => $status,
            'customers' => $customers,
            'announcements' => $announcements,
            'templates' => $templates,
            'package_speed' => $package_speed,
        ]);
    }

    public function showAll(Request $request)
    {
        return $this->index($request);
    }
    public function show(Request $request)
    {
        return $this->index($request);
    }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|unique:announcements,name|max:255',
            'template' => 'required|max:255',
            'type' => 'required|max:255',
        ])->validate();
        // $package_except = $request->package_except;
        // $packages = Package::when($request->package, function ($query, $packages) use ($package_except) {
        //     $query->where(function ($query) use ($packages, $package_except) {
        //         foreach ($packages as $package) {
        //             if ($package_except) {
        //                 $query->orwhere(function ($query) use ($package) {
        //                     $query->where('packages.speed', '<>', $package['speed'])->where('packages.type', '<>', $package['type']);
        //                 });
        //             } else {

        //                 $query->orwhere(function ($query) use ($package) {
        //                     $query->where('packages.speed', '=', $package['speed'])->where('packages.type', '=', $package['type']);
        //                 });
        //             }
        //         }
        //     });
        // })
        //     ->get()->toArray();

        $announcement = new Announcement();
        $announcement->name = $request->name;
        $announcement->general = $request->general;
        $announcement->template_id = $request->template;
        $announcement->packages_invert = $request->package_except;
        $announcement->packages = ($request->package) ? json_encode($request->package) : null;
        // if ($packages) {
        //     $announcement->packages = null;
        //     if (isset($packages[0])) {
        //         foreach ($packages as $key => $package) {

        //             if ($key != array_key_last($packages)) {
        //                 $announcement->packages .= $package['id'] . ',';
        //             } else {
        //                 $announcement->packages .= $package['id'];
        //             }
        //         }
        //     } else {
        //         $announcement->packages = $packages['id'];
        //     }
        // }
        if ($request->status) {
            $announcement->customer_status = null;
            if (isset($request->status[0])) {
                foreach ($request->status as $key => $status) {
                    if ($key != array_key_last($request->status)) {
                        $announcement->customer_status .= $status['id'] . ',';
                    } else {
                        $announcement->customer_status .= $status['id'];
                    }
                }
            } else {
                $announcement->customer_status = $request->status['id'];
            }
        }

        $announcement->announcement_type = $request->type;
        //  $announcement->townships = $request->township;
        if ($request->township) {
            $announcement->townships = null;
            if (isset($request->township[0])) {
                foreach ($request->township as $key => $township) {

                    if ($key != array_key_last($request->township)) {
                        $announcement->townships .= $township['id'] . ',';
                    } else {
                        $announcement->townships .= $township['id'];
                    }
                }
            } else {
                $announcement->townships = $request->township['id'];
            }
        }
        $announcement->customer_status_invert = $request->status_except;

        $announcement->projects = $request->partner;
        $announcement->payment_type = $request->payment;
        $announcement->deposit_status = $request->deposit_status;
        $announcement->save();
        return redirect()->back()->with('message', 'Announcement Created Successfully.');
    }
    public function update(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required|max:255|unique:announcements,name,' . $request->id,
            'template' => 'required|max:255',
            'type' => 'required|max:255',
        ])->validate();

        $announcement = Announcement::find($request->id);
        $announcement->name = $request->name;
        $announcement->general = $request->general;
        $announcement->template_id = $request->template;
        $announcement->packages_invert = ($request->package_except == 0) ? null : true;
        $announcement->packages = ($request->package) ? json_encode($request->package) : null;
        $announcement->customer_status = null;
        if ($request->status) {
            if (isset($request->status[0])) {
                foreach ($request->status as $key => $status) {
                    if ($key != array_key_last($request->status)) {
                        $announcement->customer_status .= $status['id'] . ',';
                    } else {
                        $announcement->customer_status .= $status['id'];
                    }
                }
            } else {
                $announcement->customer_status = $request->status['id'];
            }
        }

        $announcement->announcement_type = $request->type;
        $announcement->townships = $request->township;
        $announcement->customer_status_invert = ($request->status_except == 0) ? null : true;

        $announcement->projects = $request->partner;
        $announcement->payment_type = $request->payment;
        $announcement->deposit_status = $request->deposit_status;
        $announcement->update();
        return redirect()->back()->with('message', 'Announcement Updated Successfully.');
    }
    public function send(Request $request)
    {

        if ($request->id) {
            $announcement = Announcement::join('email_templates', 'email_templates.id', '=', 'announcements.template_id')
                ->where('announcements.id', '=', $request->id)
                ->select('announcements.*', 'email_templates.name as template_name', 'email_templates.title as template_title', 'email_templates.cc_email as cc_email', 'email_templates.body as template_body', 'email_templates.type as template_type','email_templates.is_bulk as is_bulk')
                ->first();
            $package_except = $announcement->packages_invert;
            $status_except = $announcement->customer_status_invert;
            $customers =  DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->when($announcement->general, function ($query, $general) {
                    $query->where(function ($query) use ($general) {
                        $query->where('customers.name', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->package, function ($query, $packages) use ($package_except) {

                    $query->where(function ($query) use ($packages, $package_except) {
                        foreach ($packages as $package) {
                            if ($package_except) {
                                $query->orwhere(function ($query) use ($package) {
                                    $query->where('packages.speed', '<>', $package['speed'])->where('packages.type', '<>', $package['type']);
                                });
                            } else {

                                $query->orwhere(function ($query) use ($package) {
                                    $query->where('packages.speed', '=', $package['speed'])->where('packages.type', '=', $package['type']);
                                });
                            }
                        }
                    });
                })
                ->when($announcement->projects, function ($query, $project) {
                    $query->where('customers.project_id', '=', $project);
                })
                ->when($announcement->townships, function ($query, $township) {
                    $query->where('customers.township_id', '=', $township);
                })
                ->when($announcement->customer_status, function ($query, $status) use ($status_except) {

                    $status_list = array();
                    $status_list = preg_split("/\,/", $status);


                    if ($status_except) {
                        $query->whereNotIn('customers.status_id', $status_list);
                    } else {
                        $query->whereIn('customers.status_id', $status_list);
                    }
                })
                ->when($announcement->payment_type, function ($query, $payment) {
                    if ($payment == 1) {
                        $query->where('customers.payment_type', '=', '1');
                    } else {
                        $query->where('customers.payment_type', '=', '0');
                    }
                })
                ->when($announcement->deposit_status, function ($query, $deposit_status) {
                    $query->where('customers.deposit_status', '=', $deposit_status);
                })
                ->when($request->sort, function ($query, $sort = null) {
                    $sort_by = 'customers.id';
                    if ($sort == 'cid') {
                        $sort_by = 'customers.id';
                    } elseif ($sort == 'cname') {
                        $sort_by = 'customers.name';
                    } elseif ($sort == 'township') {
                        $sort_by = 'townships.name';
                    } elseif ($sort == 'package') {
                        $sort_by = 'packages.name';
                    } elseif ($sort == 'order') {
                        $sort_by = 'customers.order_date';
                    }

                    $query->orderBy($sort_by, 'desc');
                }, function ($query) {
                    $query->orderBy('customers.ftth_id', 'desc');
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color')
                ->get();


            if ($customers) {
               if (!$announcement->is_bulk){
                foreach ($customers as $customer) {
                    dispatch(new AnnouncementSMSJob($customer->id, $announcement->id, count($customers), Auth::id()));
                }
               }else{
               
                dispatch(new AnnouncementSMSJob($customers, $announcement->id, count($customers), Auth::id(),true));
               }
               
            }
        }
        return redirect()->back()->with('message', 'Announcement Sent Successfully.');
    }

    public function listAll(Request $request)
    {
        $announcements = DB::table('announcements')->orderBy('id', 'desc')->paginate(10);
        return Inertia::render('Client/AnnouncementList', [
            'announcements' => $announcements,
        ]);
    }
    public function detail(Request $request)
    {
       
        if ($request->id) {
           
            $announcement = Announcement::find($request->id);
            $templates = EmailTemplate::get();
            $packages = Package::orderBy('name', 'ASC')->get();
            $townships = Township::get();
            $projects = Project::get();
            $status = Status::get();
            $package_speed = Package::select('speed', 'type')
                ->groupBy('speed', 'type')
                ->orderBy('speed', 'ASC')->get();
            $package_except = $announcement->packages_invert;
            $status_except = $announcement->customer_status_invert;
            $customers =  DB::table('customers')
                ->join('packages', 'customers.package_id', '=', 'packages.id')
                ->join('townships', 'customers.township_id', '=', 'townships.id')
                ->join('users', 'customers.sale_person_id', '=', 'users.id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->when($announcement->general, function ($query, $general) {
                    $query->where(function ($query) use ($general) {
                        $query->where('customers.name', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%');
                    });
                })
                ->when($request->package, function ($query, $packages) use ($package_except) {
                    $query->where(function ($query) use ($packages, $package_except) {
                        foreach ($packages as $package) {
                            if ($package_except) {
                                $query->orwhere(function ($query) use ($package) {
                                    $query->where('packages.speed', '<>', $package['speed'])->where('packages.type', '<>', $package['type']);
                                });
                            } else {

                                $query->orwhere(function ($query) use ($package) {
                                    $query->where('packages.speed', '=', $package['speed'])->where('packages.type', '=', $package['type']);
                                });
                            }
                        }
                    });
                })
                ->when($announcement->projects, function ($query, $project) {
                    $query->where('customers.project_id', '=', $project);
                })
                ->when($announcement->townships, function ($query, $township) {
                    $query->where('customers.township_id', '=', $township);
                })
                ->when($announcement->customer_status, function ($query, $status) use ($status_except) {
                    $status_list = array();
                    $status_list = preg_split("/\,/", $status);
                    if ($status_except) {
                        $query->whereNotIn('customers.status_id', $status_list);
                    } else {
                        $query->whereIn('customers.status_id', $status_list);
                    }
                })
                
                ->when($request->sort, function ($query, $sort = null) {
                    $sort_by = 'customers.id';
                    if ($sort == 'cid') {
                        $sort_by = 'customers.id';
                    } elseif ($sort == 'cname') {
                        $sort_by = 'customers.name';
                    } elseif ($sort == 'township') {
                        $sort_by = 'townships.name';
                    } elseif ($sort == 'package') {
                        $sort_by = 'packages.name';
                    } elseif ($sort == 'order') {
                        $sort_by = 'customers.order_date';
                    }

                    $query->orderBy($sort_by, 'desc');
                }, function ($query) {
                    $query->orderBy('customers.ftth_id', 'desc');
                })
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color')
                ->paginate(15);
            // dd($customers->toSQL(), $customers->getBindings());
          
            $customers->appends($request->all())->links();

            return Inertia::render('Client/AnnouncementDetail', [
                'announcement' => $announcement,
                'packages' => $packages,
                'townships' => $townships,
                'projects' => $projects,
                'status' => $status,
                'customers' => $customers,
                'templates' => $templates,
                'package_speed' => $package_speed,
            ]);
        }
    }

    public function sendEmail($customer_id, $template_id)
    {

        if ($customer_id) {
            $customer = Customer::whereNotNull('email')->where('customers.id', '=', $customer_id)->first();
            $email_template = EmailTemplate::where('id', '=', $template_id)
                ->where('type', '=', 'email')
                ->first();
            if ($email_template && $customer) {

                $emails = $customer->email;

                $data["email"] = (filter_var($emails, FILTER_VALIDATE_EMAIL)) ? $emails : null;
                if (strpos($emails, ',') !== false) {
                    $temp_mail = explode(",", $emails);
                    $data["email"] = array();
                    foreach ($temp_mail as $mail) {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                            array_push($data["email"], $mail);
                    }
                }
                if (strpos($emails, ';') !== false) {
                    $temp_mail = explode(";", $emails);
                    $data["email"] = array();
                    foreach ($temp_mail as $mail) {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                            array_push($data["email"], $mail);
                    }
                }
                if (strpos($emails, ':') !== false) {
                    $temp_mail = explode(":", $emails);
                    $data["email"] = array();
                    foreach ($temp_mail as $mail) {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                            array_push($data["email"], $mail);
                    }
                }
                if (strpos($emails, ' ') !== false) {
                    $temp_mail = explode(" ", $emails);
                    $data["email"] = array();
                    foreach ($temp_mail as $mail) {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                            array_push($data["email"], $mail);
                    }
                }
                if (strpos($emails, '/') !== false) {
                    $temp_mail = explode("/", $emails);
                    $data["email"] = array();
                    foreach ($temp_mail as $mail) {
                        if (filter_var($mail, FILTER_VALIDATE_EMAIL))
                            array_push($data["email"], $mail);
                    }
                }

                $email_title = $email_template->title;
                $email_body = $email_template->body;
                $email_title = $this->replaceMarkup($email_title, $customer_id);
                $email_body = $this->replaceMarkup($email_body, $customer_id);

                $cc_emails = $email_template->cc_email;
                if (strpos($cc_emails, ','))
                    $cc_emails = explode(",", $cc_emails);

                $data["cc"] = $cc_emails;
                $data["title"] = $email_title;
                $data["body"] = $email_body;
                // $attachment =  $invoice->file;
                if (!empty($data["email"])) {
                    Mail::send('emailtemplate', $data, function ($message) use ($data) {
                        $message->to($data["email"])
                            ->cc($data["cc"])
                            ->subject($data["title"]);
                        // if($attachment){
                        //     $message->attach($attachment);
                        // }
                    });
                    if (Mail::failures()) {
                        return "failed";
                    } else {
                        return "sent";
                    }
                } //check valid email exists 
            } //check $email_template && $customer
        } //check customer_id

        return "failed";
    }



    // public function updateSMSStatus(Request $request)
    // {
    //     if ($request->id) {

    //         $logs = DB::table('announcement_log')
    //             ->join('announcements', 'announcement_log.announcement_id', 'announcements.id')
    //             ->join('users', 'announcement_log.sender_id', 'users.id')
    //             ->join('customers', 'announcement_log.customer_id', 'customers.id')
    //             ->join('email_templates', 'announcement_log.template_id', 'email_templates.id')
    //             ->where('announcement_id', '=', $request->id)
    //             ->select('announcement_log.*', 'users.name as sender_name', 'customers.ftth_id as ftth_id', 'email_templates.name as template_name', 'announcements.name as announcement_name')
    //             ->get();
    //         if ($logs) {
    //             foreach ($logs as $log) {
    //                 $customer_phone = $log->phone_1;
    //                 if ($log->phone_2) {
    //                     $customer_phone .= ',' . $log->phone_2;
    //                 }
    //                 $phones = $customer_phone;
    //                 if (strpos($customer_phone, ',') !== false) {
    //                     $phones = explode(",", $customer_phone);
    //                 }
    //                 if (strpos($customer_phone, ';') !== false) {
    //                     $phones = explode(';', $customer_phone);
    //                 }
    //                 if (strpos($customer_phone, ':') !== false) {
    //                     $phones = explode(':', $customer_phone);
    //                 }
    //                 if (strpos($customer_phone, ' ') !== false) {
    //                     $phones = explode(' ', $customer_phone);
    //                 }
    //                 if (strpos($customer_phone, '/') !== false) {
    //                     $phones = explode('/', $customer_phone);
    //                 }
    //                 $success = false;
    //                 if (is_array($phones)) {
    //                     foreach ($phones as $phone) {
    //                         $pattern = "/^(959)+[0-9]+$/";
    //                         if (preg_match($pattern, $phone)) {
    //                             //$phone = '09'.$phone;
    //                             $phone = preg_split("/^959/", $phone);
    //                             // $phone = $phone[1];
    //                             $phone = '9' . $phone[1];
    //                         }
    //                         $phone_pattern = "/^(9|09)+[0-9]+$/";
    //                         if (preg_match($phone_pattern, $phone)) {

    //                                 $client = new \GuzzleHttp\Client();
    //                                 $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
    //                                 $statusresponseBody = json_decode($status_response->getBody(), true);
    //                                 if ($statusresponseBody['status'] == 'Sent') {
    //                                     $success = true;
    //                                 }

    //                         }
    //                     }
    //                 } else {
    //                     $pattern = "/^(959|\+959)+[0-9]+$/";
    //                     $phone_pattern = "/^(9|09)+[0-9]+$/";
    //                     if (preg_match($pattern, $phones)) {
    //                         //$phone = '09'.$phone;
    //                         $phones = preg_split("/(^\+959|959)/", $phones);
    //                         // $phones = $phones[1];
    //                         $phones = '9' . $phones[1];
    //                     }
    //                     if (preg_match($phone_pattern, $phones)) {


    //                             $client = new \GuzzleHttp\Client();
    //                             $status_response = $client->request('GET', self::$sms_status_url . $sms_response['messageId'], ['headers' => self::$header]);
    //                             $statusresponseBody = json_decode($status_response->getBody(), true);
    //                             if ($statusresponseBody['status'] == 'Sent') {
    //                                 $success = true;
    //                             }

    //                     }
    //                 }
    //                 if ($success) {
    //                     return "sent";
    //                 } else {
    //                     return "failed";
    //                 }
    //             }
    //         }
    //     }
    // }
    public function log(Request $request)
    {
        if ($request->id) {
            $announcement = Announcement::find($request->id);
            $log = DB::table('announcement_log')
                ->join('announcements', 'announcement_log.announcement_id', 'announcements.id')
                ->join('users', 'announcement_log.sender_id', 'users.id')
                ->join('customers', 'announcement_log.customer_id', 'customers.id')
                ->join('email_templates', 'announcement_log.template_id', 'email_templates.id')
                ->when($request->general, function ($query, $general) {
                    $query->where(function ($query) use ($general) {
                        $query->where('customers.name', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                            ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%');
                    });
                })
                ->where('announcement_id', '=', $request->id)
                ->select('announcement_log.*', 'users.name as sender_name', 'customers.ftth_id as ftth_id', 'email_templates.name as template_name', 'announcements.name as announcement_name')
                ->paginate(15);
            return Inertia::render('Client/AnnouncementLog', [
                'logs' => $log,
                'announcement' => $announcement
            ]);
        }
    }
}
