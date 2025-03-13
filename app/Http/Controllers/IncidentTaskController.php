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
use App\Events\AddIncident;
use App\Events\UpdateIncident;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewIncidentNotification;
use App\Notifications\NewTaskNotification;

class IncidentTaskController extends Controller
{
    public function index(Request $request)
    {
        //Auth::id();
        $permission =  DB::table('roles')
            ->join('users', 'users.role', '=', 'roles.id')
            ->where('users.id', '=', Auth::id())
            ->select('roles.write_incident', 'roles.read_incident')
            ->get();
        $noc = DB::table('users')
            ->join('roles', 'users.role', '=', 'roles.id')
            //  ->where('roles.name', 'LIKE', '%noc%')
            ->where('users.disabled', '=', 0)
            ->select('users.name as name', 'users.id as id')
            ->get();
        $user = User::find(Auth::id());
        $tasks = DB::table('tasks as t')
            ->join('incidents as i', 'i.id', 't.incident_id')
            ->join('customers as c', 'c.id', 'i.customer_id')
            ->join('users as u1', 'u1.id', 'i.incharge_id')
            ->when($request->keyword, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('c.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('i.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('t.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->task, function ($query, $task) {
                if ($task == 'my')
                    $query->whereJsonContains('assigned', [['id' => Auth::id()]]);
            }, function ($query) {
                $query->whereJsonContains('assigned', [['id' => Auth::id()]]);
            })
            ->when($request->status, function ($query, $status) {
                if ($status == 1 || $status == 2)
                    $query->where('t.status', '=', $status);
            }, function ($query) {
                $query->where('t.status', '=', 1);
            })
            ->select(
                't.*',
                'u1.name as incharge',
                'c.ftth_id',
                'i.priority',
                'i.type',
                'i.topic',
                'i.description as incident_description',
                'i.date',
                'i.time'
            )
            ->orderBy('t.id', 'DESC')
            ->paginate(10);

        $tasks->appends($request->all())->links();
        return Inertia::render(
            'Client/IncidentTask',
            [
                'tasks' => $tasks,
                'permission' => $permission,
                'noc' => $noc,
                'user' => $user,
            ]
        );
    }


    public function update(Request $request, $id)
    {

        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
        ])->validate();
        if ($request->has('id')) {
            $task = Task::find($request->input('id'));
            $task->incident_id = $request->incident_id;
            $task->assigned = json_encode($request->assigned);
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

            $notiUsers  = User::whereHas('role', function ($query) {
                $query->where('enable_incident_notification', 1);
            })->get();
            $notificationMessage = 'Task Updated';
            $notificationAction = 'updated';
            // Send notification to users
            Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
            return redirect()->back()->with('message', 'Task Updated Successfully.');
        }
    }
    public function updateStatus($incident_id)
    {

        $tasks = Task::where('incident_id', '=', $incident_id)->get();
        if ($tasks) {
            $completed = true;
            foreach ($tasks as $task) {
                if ($task->status != 2)
                    $completed = false;
            }
            if ($completed) {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 5]);
                if ($update) {
                    broadcast(new UpdateIncident($incident_id, 5))->toOthers();
                }
            } else {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 2]);
                if ($update) {
                    broadcast(new UpdateIncident($incident_id, 2))->toOthers();
                }
            }
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