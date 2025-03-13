<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use App\Models\IncidentHistory;
use App\Models\Task;
use App\Models\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;

use Illuminate\Support\Facades\Notification;
use App\Notifications\NewIncidentNotification;
use App\Notifications\NewTaskNotification;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        //Auth::id();
        $permission =  DB::table('roles')
            ->join('users', 'users.role', '=', 'roles.id')
            ->where('users.id', '=', Auth::id())
            ->select('roles.write_incident', 'roles.read_incident')
            ->get();
        $user = User::find(Auth::id());
        $townships = Township::get();
        $packages = Package::get();
        $critical = Incident::where('priority', '=', 'critical')->where('status', '!=', 3)->where('status', '!=', 4)->count();
        $high = Incident::where('priority', '=', 'high')->where('status', '!=', 3)->where('status', '!=', 4)->count();
        $normal = Incident::where('priority', '=', 'normal')->where('status', '!=', 3)->where('status', '!=', 4)->count();
        $noc = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            //  ->where('roles.name', 'LIKE', '%noc%')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $team = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            ->select('users.name as name', 'users.id as id')
            ->get();

        $customers = Customer::select('id', 'ftth_id')->where(function ($query) {
            return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
        })->get();
        $orderby = null;
        if ($request->sort && $request->order) {
            $orderby = $request->sort . ' ' . $request->order;
        }


        $incidents =  DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'incidents.incharge_id', '=', 'users.id')
            ->when($request->status, function ($query, $status) {
                $query->where('incidents.status', '=', $status);
            }, function ($query) {
                $query->whereRaw('incidents.status in (1,2,5)');
            })
            ->when($request->keyword, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('incidents.type', '=', $type);
            })
            ->when($request->member, function ($query, $member) {
                $query->where('users.id', '=', $member);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('incidents.priority', '=', $priority);
            })
            ->when($request->date, function ($query, $date) {
                $d = explode(',', $date);
                $from = date($d[0]);
                $to = date($d[1]);
                $query->whereBetween('incidents.date', [$from, $to]);
            })
            ->when($orderby, function ($query, $sort) {
                $query->orderByRaw($sort);
            }, function ($query) {
                $query->orderBy('incidents.id', 'DESC');
            })
            ->select(
                'incidents.*',
                'incidents.status as status',
                'customers.ftth_id as ftth_id',
                'townships.short_code as township_short',
                'customers.id as customer_id',
                'users.name as incharge'
            )
            ->paginate(10);

        // foreach ($incidents as $value) {
        //     $max_invoice_id =  DB::table('tasks')
        //                                 ->where('tasks.invoice_id', '=', $value->id)
        //                                 ->latest('id')->first();
        // }

        $incidents_2 =  DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('users', 'incidents.incharge_id', '=', 'users.id')
            ->when($request->status, function ($query, $status) {
                $query->where('incidents.status', '=', $status);
            }, function ($query) {
                $query->whereRaw('incidents.status in (1,2,5)');
            })
            ->when($request->keyword, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($orderby, function ($query, $sort) {
                $query->orderByRaw($sort);
            }, function ($query) {
                $query->orderBy('incidents.id', 'DESC');
            })
            ->select(
                'incidents.*',
                'incidents.status as status',
                'customers.ftth_id as ftth_id',
                'customers.id as customer_id',
                'users.name as incharge',
            )
            ->get();
        $incidents->appends($request->all())->links();
        return Inertia::render(
            'Client/Incident',
            [
                'incidents' => $incidents,
                'incidents_2' => $incidents_2,
                'packages' => $packages,
                'noc' => $noc,
                'team' => $team,
                'townships' => $townships,
                'customers' => $customers,
                'critical' => $critical,
                'high' => $high,
                'normal' => $normal,
                'permission' => $permission,
                'user'=>$user
            ]
        );
    }

    public function getTask($id)
    {
        if ($id) {
            $tasks = DB::table('tasks')
                ->where('tasks.incident_id', '=', $id)
                ->where('tasks.status', '<>', 0)
                ->orderBy('tasks.id', 'DESC')
                ->get();
            return response()->json($tasks, 200);
        }
    }
    public function getIncident($id)
    {
        if ($id) {
            $incident = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('users', 'incidents.incharge_id', '=', 'users.id')
                ->where('incidents.id', '=', $id)
                ->select(
                    'incidents.*',
                    'incidents.status as status',
                    'customers.ftth_id as ftth_id',
                    'customers.id as customer_id',
                    'users.name as incharge',
                )

                ->first();
            return response()->json($incident, 200);
        }
    }
    public function getLog($id)
    {
        if ($id) {
            $tasks = DB::table('incident_histories')
                ->where('incident_histories.incident_id', '=', $id)
                ->join('users', 'incident_histories.actor_id', '=', 'users.id')
                ->orderBy('incident_histories.id', 'DESC')
                ->select('users.name as name', 'incident_histories.*')
                ->get();
            return response()->json($tasks, 200);
        }
    }
    public function getCustomer($id)
    {
        if ($id) {
            $customer = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('packages', 'packages.id', '=', 'customers.package_id')
                ->where('incidents.id', '=', $id)
                ->select('customers.*', 'packages.name as package_name', 'packages.speed as package_speed', 'packages.price as package_price', 'packages.currency as package_currency', 'packages.contract_period as package_contract_period')
                ->get();
            return response()->json($customer, 200);
        }
    }
    public function getHistory($id)
    {
        if ($id) {
            $incident =  Incident::find($id);
            if ($incident) {
                $tasks = DB::table('incidents')
                    ->join('users', 'incidents.incharge_id', '=', 'users.id')
                    ->join('customers', 'customers.id', '=', 'incidents.customer_id')
                    ->where('customers.id', '=', $incident->customer_id)
                    ->orderBy('incidents.id', 'DESC')
                    ->select('users.name', 'incidents.date', 'incidents.time', 'incidents.code', 'customers.ftth_id', 'incidents.status', 'incidents.type', 'incidents.topic')
                    ->get();
                return response()->json($tasks, 200);
            }
        }
    }
    public function getFile($id)
    {
        if ($id) {

            $files = DB::table('file_uploads')
                ->where('file_uploads.incident_id', '=', $id)
                ->orderBy('file_uploads.id', 'DESC')
                ->get();
            return response()->json($files, 200);
        }
    }
    public function getCustomerFile($id)
    {
        if ($id) {

            $files = DB::table('file_uploads')
                ->where('file_uploads.customer_id', '=', $id)
                ->orderBy('file_uploads.id', 'DESC')
                ->get();
            return response()->json($files, 200);
        }
    }
    public function deleteFile(Request $request, $id)
    {
        if ($request->has('id')) {
            File::delete(public_path($request->input('path')));
            FileUpload::find($request->input('id'))->delete();
            if (isset($request->incident_id)) {
                $data = array();
                $data['incident_id'] = $request->incident_id;
                $data['action'] = 'File Deleted:' . $request->name;
                $data['datetime'] = date('Y-m-j h:m:s');
                $data['actor_id'] = Auth::id();
                $this->insertHistory($data);
            }

            return redirect()->back();
        }
    }
    public function addTask(Request $request)
    {

        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'assigned' => ['required'],
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ])->validate();
        $task = new Task();
        $task->incident_id = $request->incident_id;
        $task->assigned = json_encode($request->assigned);
        $task->target = $request->target;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->created_at = NOW();
        $task->updated_at = NOW();
        $task->save();


        $data = array();
        $data['incident_id'] = $request->incident_id;
        $data['action'] = 'Task Created:' . $request->description;
        $data['datetime'] = date('Y-m-j h:m:s');
        $data['actor_id'] = Auth::id();
        $this->updateStatus($request->incident_id);
        $this->insertHistory($data);
        // $notiUsers  = User::whereHas('role', function ($query) {
        //     $query->where('enable_incident_notification', 1);
        // })->get();
        // $notificationMessage = 'Task Created';
        // $notificationAction = 'created';
        // Send notification to users
       // Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
        return redirect()->back()->with('message', 'Task Created Successfully.');
    }
    public function editTask(Request $request, $id)
    {

        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'assigned' => ['required'],
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ])->validate();
        if ($request->has('id')) {
            $task = Task::find($request->input('id'));
            $task->incident_id = $request->incident_id;
            $task->assigned = $this->isJsonObject($request->assigned) ? $request->assigned : json_encode($request->assigned);
            $task->target = $request->target;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->updated_at = NOW();
            $task->update();
            $data = array();

            $data['incident_id'] = $request->incident_id;
            $data['action'] = 'Task Updated:' . $request->description;
            $data['datetime'] = date('Y-m-j h:m:s');
            $data['actor_id'] = Auth::id();
            $this->updateStatus($request->incident_id);
            $this->insertHistory($data);
            // $notiUsers  = User::whereHas('role', function ($query) {
            //     $query->where('enable_incident_notification', 1);
            // })->get();
            // $notificationMessage = ($request->status == 0) ? 'Task Deleted' : 'Task Updated';
            // $notificationAction = ($request->status == 0) ? 'deleted' : 'updated';
            // // Send notification to users
            // Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
             return redirect()->back()->with('message', 'Task Updated Successfully.');
        }
    }
    function isJsonObject($jsonString)
    {
        try {
            $decodedData = json_decode($jsonString);
            return $decodedData !== null;
        } catch (\Throwable $th) {

            return false;
        }


        // Check if decoding was successful and the result is an object

    }
    public function updateStatus($incident_id)
    {

        $tasks = Task::where('incident_id', '=', $incident_id)->where('status', '<>', 0)
            ->get();
        if ($tasks) {
            $completed = true;
            foreach ($tasks as $task) {
                if ($task->status != 2)
                    $completed = false;
            }
            if ($completed) {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 5]);
                //    if($update){
                //     broadcast(new UpdateIncident($incident_id,5))->toOthers();
                //    }
            } else {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 2]);
                // if($update){
                //     broadcast(new UpdateIncident($incident_id,2))->toOthers();
                // }
            }
        }
    }


    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'date' => ['required'],
            'priority' => ['required'],
            'time' => ['required'],
            'incharge_id' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'description' => ['required'],
            'customer_id' => ['required'],
        ])->validate();
        // dd($request->all());
        if ($request->customer_id['id']) {
            $incident = new Incident();
            // $incident->code = $request->code;
            $incident->customer_id = $request->customer_id['id'];
            $incident->incharge_id = $request->incharge_id['id'];
            $incident->type = $request->type;
            $incident->priority = $request->priority;
            $incident->topic = $request->topic;
            $incident->status = $request->status;

            if ($request->type == 'plan_change') {
                $myDateTime = new DateTime;
                $newtime = clone $myDateTime;
                if ($request->start_date)
                    $myDateTime = new DateTime($request->start_date);
                if ($myDateTime->format('d') <= 7) {
                    $newtime->modify('first day of this month');
                    $incident->start_date = $newtime->format('Y-m-j h:m:s');
                } else {
                    $newtime->modify('+1 month');
                    $newtime->modify('first day of this month');
                    $incident->start_date = $newtime->format('Y-m-j h:m:s');
                }
            } else {
                $incident->start_date = $request->start_date;
            }


            $incident->end_date = $request->end_date;
            if (!empty($request->new_township)) {
                $incident->new_township = $request->new_township['id'];
            }

            $incident->new_address = $request->new_address;
            if (!empty($request->latitude) && !empty($request->longitude))
                $incident->location = $request->latitude . ',' . $request->longitude;
            if (!empty($request->package_id)) {
                $incident->package_id = $request->package_id['id'];
            }

            if (isset($request->close_date) && $request->status == 3)
                $incident->close_date = $request->close_date;

            if (isset($request->close_time) && $request->status == 3)
                $incident->close_time = $request->close_time;

            if (isset($request->resolved_date) && $request->status == 5)
                $incident->resolved_date = $request->resolved_date;

            if (isset($request->resolved_time) && $request->status == 5)
                $incident->resolved_time = $request->resolved_time;

            $incident->date = $request->date;
            $incident->time = $request->time;
            $incident->description = $request->description;
            $incident->save();
            $incident->code = 'T-' . str_pad($incident->id, 4, "0", STR_PAD_LEFT);
            $incident->update();


            $data = array();
            $data['incident_id'] = $incident->id;
            $data['action'] = 'Incident Created';
            $data['datetime'] = date('Y-m-j h:m:s');
            $data['actor_id'] = Auth::id();
            $this->insertHistory($data);
            $incident_data =  DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->select(
                    'incidents.*',
                    'incidents.status as status',
                    'customers.ftth_id as ftth_id',
                    'customers.id as customer_id',
                )->first();
            // broadcast(new AddIncident($incident_data))->toOthers();
            // $notiUsers  = User::whereHas('role', function ($query) {
            //     $query->where('enable_incident_notification', 1);
            // })->get();
            // Notification::send($notiUsers, new NewIncidentNotification($incident, 'Incident Created', 'created'));
            return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.')
                ->with('id', $incident->id);
            return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.')
                ->with('id', $incident->id);
        }
    }


    function console_log($output, $with_script_tags = true)
    {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'code' => ['required'],
            'priority' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'incharge_id' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'description' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $old_incident = Incident::find($request->input('id'));

            $update = $this->checkUpdate($old_incident, $request->request);

            if ($update) {
                $data = array();
                $data['incident_id'] = $request->input('id');
                $data['action'] = 'Incident Update :' . $update;
                $data['datetime'] = date('Y-m-j h:m:s');
                $data['actor_id'] = Auth::id();
                $this->insertHistory($data);
            }


            if ($update) {
                $incident = Incident::find($request->input('id'));
                $incident->code = $request->code;
                $incident->customer_id = $request->customer_id['id'];
                $incident->incharge_id = $request->incharge_id['id'];
                $incident->type = $request->type;
                $incident->priority = $request->priority;
                $incident->topic = $request->topic;
                $incident->status = $request->status;
                if ($request->type == 'plan_change') {
                    $myDateTime = new DateTime;
                    $newtime = clone $myDateTime;
                    if ($request->start_date)
                        $myDateTime = new DateTime($request->start_date);
                    if ($myDateTime->format('d') <= 7) {
                        $newtime->modify('first day of this month');
                        $incident->start_date = $newtime->format('Y-m-j h:m:s');
                    } else {
                        $newtime->modify('+1 month');
                        $newtime->modify('first day of this month');
                        $incident->start_date = $newtime->format('Y-m-j h:m:s');
                    }
                } else {
                    $incident->start_date = $request->start_date;
                }
                $incident->end_date = $request->end_date;
                if (!empty($request->new_township)) {
                    $incident->new_township = $request->new_township['id'];
                }

                $incident->new_address = $request->new_address;
                if (!empty($request->latitude) && !empty($request->longitude))
                    $incident->location = $request->latitude . ',' . $request->longitude;
                if (!empty($request->package_id)) {
                    $incident->package_id = $request->package_id['id'];
                }
                $incident->date = $request->date;
                $incident->time = $request->time;

                if (isset($request->close_date) && $request->status == 3)
                    $incident->close_date = $request->close_date;

                if (isset($request->close_time) && $request->status == 3)
                    $incident->close_time = $request->close_time;

                if (isset($request->resolved_date) && $request->status == 5)
                    $incident->resolved_date = $request->resolved_date;

                if (isset($request->resolved_time) && $request->status == 5)
                    $incident->resolved_time = $request->resolved_time;

                $incident->description = $request->description;


                //  broadcast(new UpdateIncident($request->input('id'),$request->status))->toOthers();

                $incident->closed_by = Auth::user()->id;
                $incident->update();
                // $notiUsers  = User::whereHas('role', function ($query) {
                //     $query->where('enable_incident_notification', 1);
                // })->get();
                // $notificationMessage = ($request->status == 4) ? 'Incident Deleted' : 'Incident Updated';
                // $notificationAction = ($request->status == 4) ? 'deleted' : 'updated';
                // Send notification to users
                // Notification::send($notiUsers, new NewIncidentNotification($incident, $notificationMessage, $notificationAction));
            }

            return redirect()->route('incident.index')->with('message', 'Incident Updated Successfully.');
        }
    }
    public function getStatus($data)
    {
        $status = "Open";
        if ($data == 1) {
            $status = "Open";
        } else if ($data == 2) {
            $status = "Escalated";
        } else if ($data == 3) {
            $status = "Closed";
        } else if ($data == 4) {
            $status = "Deleted";
        } else if ($data == 5) {
            $status = "Resolved";
        }
        return $status;
    }
    public function insertHistory($data)
    {
        $ih = new IncidentHistory();

        $ih->incident_id = $data['incident_id'];
        $ih->action  = $data['action'];
        $ih->datetime = $data['datetime'];
        $ih->actor_id = $data['actor_id'];
        $ih->created_at = date("Y-m-j h:m:s");
        $ih->save();
    }
    public function checkInsertData($data)
    {
        $insert = null;
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                if ($key == "customer_id") {
                    $insert .= $key . ':' . $value["name"] . ',';
                } else if ($key == "incharge_id") {
                    $insert .= $key . ':' . $value["name"] . ',';
                } else if ($key == "date" || $key == "start_date" || $key == "end_date") {
                    $insert .= $key . ':' . ucwords($value) . ',';
                } else  if ($key == "time") {
                    $insert .=  $key . ':' . ucwords($value) . ',';
                } else if ($key == "status") {
                    $insert .=  $key . ':' . $this->getStatus($value) . ',';
                } else {
                    $insert .=  $key . ':' . ucwords($value) . ',';
                }
            }
        }
        return $insert;
    }
    public function checkUpdate($old, $new)
    {

        $update = null;
        foreach ($new as $key => $value) {
            if (isset($old->$key) && !empty($key)) {

                if ($key == "customer_id") {
                    $update .= ($old->customer_id != $value['id']) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "incharge_id") {
                    $update .= ($old->incharge_id != $value['id']) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "date" || $key == "start_date" || $key == "end_date") {
                    $update .= (date("Y-m-j", strtotime($old->$key)) != $value) ? $key . ':' . ucwords($value) . ',' : '';
                } else  if ($key == "time") {
                    $update .= ($old->$key != strtotime($value)) ? $key . ':' . ucwords($value) . ',' : '';
                } else if ($key == "status") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $this->getStatus($value) . ',' : '';
                } else if ($key == "package_id") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "new_township") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $value["name"] . ',' : '';
                } else {
                    try {
                        $update .= ($old->$key != $value) ? $key . ':' . ucwords($value) . ',' : '';
                    } catch (\Throwable $th) {
                        dd($key);
                    }
                }
            }
        }

        return $update;
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Incident::find($request->input('id'))->delete();

            return redirect()->back();
        }
    }
}
