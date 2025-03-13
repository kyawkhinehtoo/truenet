<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SnPorts;
use App\Models\DnPorts;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
class SNPortController extends Controller
{
    public function index(Request $request)
    {   
        $dns = DnPorts::get();
        $sns = DB::table('sn_ports')
        ->when($request->keyword, function ($query, $keyword){
            $query->where('name','LIKE','%'.$keyword.'%');
            $query->orwhere('description','LIKE','%'.$keyword.'%');
        })
        ->paginate(10);
        $sns_all = SnPorts::get();
        $overall = DB::table('sn_ports')
                        ->select(DB::raw('name, count(port) as ports,dn_id'))
                        ->groupBy(['dn_id','name'])
                        ->paginate(20);
        $sns->appends($request->all())->links();
        return Inertia::render('Setup/SNPorts',
         ['sns' => $sns, 'dns' => $dns,'overall'=>$overall, 'sns_all'=>$sns_all]);
    }

    // public function generateSN(){
    //     $dns = DnPorts::get();
    //     foreach ($dns as $key => $value) {
    //         echo $value->name." : ". $value->port ."<br />";
    //         for($n=1; $n <=16 ; $n++ ){
    //          $snport = new SnPorts();
    //          $snport->dn_id = $value->id;
    //          $snport->name = "SN0".$value->port;
    //          $snport->port = $n;
    //          $snport->description = "";
    //          $snport->save();
    //          echo "Created New SN Port: SN0".$value->port." Port: ".$n."<br/>";
    //         }
    //     }
    // }
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'dn_id' => ['required'],
            'port' => ['required'],
        ])->validate();
          
        if($request->tab == 2){  
            $check_dup = SnPorts::where('dn_id',$request->dn_id['id'] )
                        ->where('name',$request->sn['name'])
                        ->where('port',$request->port)
                        ->exists();
            if($check_dup)
            {
                return redirect()->back()->withErrors('Port Number Already Exist!');
               
            }else{
                $snport = new SnPorts();
                $snport->dn_id = $request->dn_id['id'];
                $snport->name = $request->sn['name'];
                $snport->port = $request->port;
                $snport->description = $request->description;
                $snport->save();
                return redirect()->back()->with('message', 'Port Created Successfully.');
            }
            
        }else{
            for($n=1; $n <=$request->port ; $n++ ){
                $snport = new SnPorts();
                $snport->dn_id = $request->dn_id['id'];
                $snport->name = $request->name;
                $snport->port = $n;
                $snport->description = $request->description;
                $snport->save();
               
            }
            return redirect()->back()->with('message', 'Ports Created Successfully.');
        }    
       
       
        
        
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'dn_id' => ['required'],
            'name' => ['required'],
            'port' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $snport = SnPorts::find($request->input('id'));
            $snport->dn_id = $request->dn_id['id'];
            $snport->name = $request->name;
            $snport->port = $request->port;
            $snport->description = $request->description;
            $snport->update();
            return redirect()->back()
                    ->with('message', 'Port Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            SnPorts::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id){
        if ($request->has('id')) {
            SnPorts::where('dn_id','=',$request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
}
