<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Township;

use App\Models\Package;
use App\Models\Role;
use App\Models\User;

use App\Models\Customer;
use App\Models\CustomerHistory;
use App\Models\Incident;
use App\Models\IncidentHistory;

use App\Models\Status;
use App\Models\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;
class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {   
        $sorting = array('cid','asc');
        if($request->sort){
            $sorting[0]=$request->sort;
        }
        if($request->order){
            $sorting[1]=$request->order;
        }
     
        $closed = ($request->status=='close')?true:false;
  
         $packages = Package::get();
         $townships = Township::get();
         $users = User::get();
         $status = Status::get();
         $incidents = DB::table('incidents')
                        ->join('users','users.id','=','incidents.incharge_id')
                        ->join('customers','customers.id','=','incidents.customer_id')
                        ->join('status','customers.status_id','=','status.id')
                        ->join('packages','customers.package_id','=','packages.id')
                        ->whereIn('incidents.type',['relocation','termination','plan_change','suspension','resume'])
                        ->when($closed, function ($query,$closed) {
          
                            $query->whereIN('incidents.status', [3,4]);
                        }, function ($query) {
                            $query->whereIN('incidents.status', [1,2]);
                        })
         ->when($request->general, function($query, $general){
            $query->where(function ($query) use ($general) {
                $query->where('customers.name', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                    ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%')
                    ->orWhere('incidents.type', 'LIKE', '%' . $general . '%')
                    ->orWhere('incidents.code', 'LIKE', '%' . $general . '%');
            });
         })
         ->when($sorting, function ($query, $sort = null) {
          
            $sort_by = 'customers.id';
            if ($sort[0] == 'id') {
                $sort_by = 'incidents.id';
            } elseif ($sort[0] == 'date') {
                $sort_by = 'incidents.date';
            } elseif ($sort[0] == 'cid') {
                $sort_by = 'customers.ftth_id';
            } elseif ($sort[0] == 'type') {
                $sort_by = 'incidents.type';
            } elseif ($sort[0] == 'start') {
                $sort_by = 'incidents.start_date';
            }elseif ($sort[0] == 'end') {
                $sort_by = 'incidents.end_date';
            }elseif ($sort[0] == 'request') {
                $sort_by = 'incidents.incharge_id';
            }elseif ($sort[0] == 'status') {
                $sort_by = 'incidents.status';
            }

            $query->orderBy($sort_by,$sort[1]);
        }, function ($query) {
            $query->orderBy('incidents.id','ASC');
        })
        
         ->select('incidents.*','customers.id as customer_id','customers.ftth_id as ftth_id','customers.package_id as current_package','incidents.package_id as new_package','users.name as incharge','customers.address as current_address','customers.township_id as current_township','status.name as status_name','status.id as status_id')
         ->paginate(10);
         $incidents->appends($request->all())->links();
        return Inertia::render('Client/ServiceRequest', 
        [  
            'incidents' => $incidents,
            'packages' => $packages,
            'townships' => $townships,
            'users' => $users,
            'status' => $status,
        ]);

    }
    public function update(Request $request){
        if($request->has('id'))
        {
            if($request->type)
           {
           
                $customer = Customer::find($request->input('customer_id'));
                if($request->new_address)
                $customer->address = $request->new_address;
                if($customer->new_location)
                $customer->location = $request->new_location;
                if ($request->new_package)
                $customer->package_id = $request->new_package['id'];
                if($request->new_township){
                    $customer->township_id = $request->new_township['id'];
                }
                if ($request->type == 'termination'){
                    $status = Status::where('name','LIKE','%Termina%')->first();
                    $customer->status_id = $status->id;
                }
                if ($request->type == 'suspension'){
                    $status = Status::where('name','LIKE','%Suspen%')->first();
                    $customer->status_id = $status->id;
                }
                if ($request->type == 'resume'){
                    $status = Status::where('name','=','Active')->first();
                    $customer->status_id = $status->id;
                }
                    
                $customer->update();

                CustomerHistory::where('customer_id', '=', $request->customer_id)->update(['active'=>0]);

                $new_history = new CustomerHistory();
                $new_history->old_status = $request->status_id;
                $new_history->customer_id = $customer->id;
                $new_history->type = $request->type ;
                $new_history->actor_id = Auth::user()->id;
                $new_history->active = 1;
                $new_history->date = date("Y-m-j h:m:s");
               
                if ($request->start_date){
                    $new_history->start_date = $request->start_date;
                }
                
                if ($request->end_date)
                    $new_history->end_date = $request->end_date;
        
                if ($request->type == 'relocation'){
                    //new
                    if ($request->new_address)
                    $new_history->new_address = $request->new_address;
                    if ($request->new_location)
                    $new_history->new_location = $request->new_location;
                    //old
                    $new_history->old_address = $request->current_address;
                    $new_history->old_location = $request->latitude . ',' . $request->longitude;

                    $status = Status::where('name','=','Active')->first();
                    $new_history->new_status = $status->id;
                }
                if ($request->type == 'termination'){
                    $status = Status::where('name','LIKE','%Termina%')->first();
                    $new_history->new_status = $status->id;
                }
                if ($request->type == 'suspension'){
                    $status = Status::where('name','LIKE','%Suspen%')->first();
                    $new_history->new_status = $status->id;
                }
                if ($request->type == 'resume'){
                    $status = Status::where('name','=','Active')->first();
                    $new_history->new_status = $status->id;
                }
                if ($request->type == 'plan change'){
                    //new
                    if ($request->new_package)
                    $new_history->new_package = $request->new_package['id'];
                    //old
                    if ($request->current_package)
                    $new_history->old_package = $request->current_package['id'];
                
                    // $status = Status::where('name','=','Active')->first();
                    // $new_history->new_status = $status->id;
                    $myDateTime = new DateTime;
                    $newtime = clone $myDateTime;
                    if ($request->start_date){
                        $myDateTime = new DateTime($request->start_date);
                        $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    }
                   
                    // if($myDateTime->format('d') <= 7){
                    //     $newtime->modify('first day of this month');
                    //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    // }else{
                    //     $newtime->modify('+1 month');
                    //     $newtime->modify('first day of this month');
                    //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    // }
                }
                $new_history->save();

                $incident = Incident::find($request->input('id'));
                $incident->status = 3;
                $incident->close_date = date("Y-m-j h:m:s");
                $incident->close_time = date("h:m:s");
                $incident->update();
                //close the incident

                $ih = new IncidentHistory();

                $ih->incident_id = $request->input('id'); 
                $ih->action  = 'Incident closed';
                $ih->datetime = date("Y-m-j h:m:s");
                $ih->actor_id = Auth::user()->id;
                $ih->created_at = date("Y-m-j h:m:s");
                $ih->save();
                
                
                return redirect()->route('servicerequest.index')->with('message', 'Incident has been Processed.');
           }
           // $customer = Customer::find($request->input('customer_id'));
            //if($request->status)
            //$customer->status_id = $request->status['id'];
        }
    }
}
