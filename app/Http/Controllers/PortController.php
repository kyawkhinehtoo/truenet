<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\Pop;
use App\Models\PopDevice;
use Hamcrest\Arrays\IsArray;
use Hamcrest\Type\IsNumeric;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PortController extends Controller
{
    public function index(Request $request)
    {

        // $dns = DB::table('dn_ports')
        //     ->when($request->general, function ($query, $general) {
        //         $query->where('name', 'LIKE', '%' . $general . '%');
        //         $query->orwhere('description', 'LIKE', '%' . $general . '%');
        //     })
        //     ->paginate(10);
        $pops = Pop::all();
        $dns_all = DnPorts::get();
        $overall = DB::table('dn_ports')
        ->leftJoin('sn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
        ->select(
            'dn_ports.id',
            'dn_ports.name',
            'dn_ports.description',
            'dn_ports.location',
            'dn_ports.pop',
            'dn_ports.input_dbm',
            DB::raw('count(sn_ports.id) as ports'),
            'dn_ports.pop_device_id',
            'dn_ports.gpon_frame',
            'dn_ports.gpon_slot',
            'dn_ports.gpon_port'
        )
        ->when($request->general, function ($query, $general) {
            $query->where('dn_ports.name', 'LIKE', '%' . $general . '%')
                  ->orWhere('dn_ports.description', 'LIKE', '%' . $general . '%');
        })
        ->when($request->pop, function ($query, $pops) {
            $pop_id = array_map(fn($pop) => $pop['id'], $pops);
            $query->where(function ($q) use ($pop_id) {
                foreach ($pop_id as $id) {
                    $q->orWhere('dn_ports.pop', $id);
                }
            });
        })
        ->groupBy(
            'dn_ports.id',
            'dn_ports.name',
            'dn_ports.description',
            'dn_ports.location',
            'dn_ports.pop',
            'dn_ports.input_dbm',
            'dn_ports.pop_device_id',
            'dn_ports.gpon_frame',
            'dn_ports.gpon_slot',
            'dn_ports.gpon_port'
        )
        ->orderBy('dn_ports.id')
        ->paginate(10);
        $overall->appends($request->all())->links();
        return Inertia::render(
            'Setup/Ports',
            ['overall' => $overall, 'dns_all' => $dns_all, 'pops' => $pops]
        );
    }
    public function getIdByName(Request $request)
    {
        $sn = null;
        if ($request->name) {

            $sn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->where('dn_ports.name', '=', $request->name)
                ->select('sn_ports.id as id', 'sn_ports.name as name', 'sn_ports.port as port', 'sn_ports.description as description', 'dn_ports.name as dn_name')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function getOLTByPOP($request)
    {

        $pop_devices = null;
        if ($request && is_numeric($request)) {
            $pop_devices = PopDevice::where('pop_id', (int)$request)
                ->get();
        }
        return response()
            ->json($pop_devices, 200);
    }
    public function getDNByOLT($request)
    {
        $dn = null;
        if ($request && is_numeric($request)) {
            $dn = DnPorts::where('pop_device_id', (int)$request)
                ->get();
        }

        return response()
            ->json($dn, 200);
    }
    public function getSNByDN($request)
    {

        $sn = null;
        if ($request && is_numeric($request)) {

            $sn = DB::table('sn_ports')
                // ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                // ->join('pops', 'dn_ports.pop', '=', 'pops.id')
                // ->join('pop_devices', 'dn_ports.pop_device_id', '=', 'pop_devices.id')
                ->where('sn_ports.dn_id', '=', $request)
                ->select(
                    'sn_ports.id as id',
                    'sn_ports.name as name',
                    'sn_ports.port as port',
                    'sn_ports.description as description',
                    'sn_ports.dn_id as dn_id'
                )
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function getDNInfo($request)
    {
        $dn = null;
        if ($request && is_numeric($request)) {
            $dn = DB::table('dn_ports')
                ->where('dn_ports.id', '=', $request)
                ->select(
                    'dn_ports.name as dn_name',
                    'dn_ports.pop as pop_id',
                    'dn_ports.pop_device_id as pop_device_id',
                    'dn_ports.gpon_frame',
                    'dn_ports.gpon_slot',
                    'dn_ports.gpon_port'
                )
                ->get();
        }
        return response()
            ->json($dn, 200);
    }



    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();


        $check_dup = DnPorts::where('name', $request->name)
            ->exists();
        if ($check_dup) {
            return redirect()->back()->withErrors('DN Already Exist!');
        } else {

            $dnport = new DnPorts();
            $dnport->name = $request->name;
            $dnport->pop = $request->pop ? $request->pop['id'] : null;
            $dnport->pop_device_id = $request->pop_device_id ? $request->pop_device_id['id'] : null;
            $dnport->gpon_frame = $request->gpon_frame;
            $dnport->gpon_slot = $request->gpon_slot;
            $dnport->gpon_port = $request->gpon_port;
            $dnport->description = $request->description;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->save();
            return redirect()->back()->with('message', 'DN Port Created Successfully.');
        }

        // }else{
        // for($n=1; $n <=$request->port ; $n++ ){
        //     $dnport = new DnPorts();
        //     $dnport->name = $request->name;
        //     $dnport->port = $n;
        //     $dnport->description = $request->description;
        //     $dnport->save();
        // }

        //  return redirect()->back()->with('message', 'DN Created Successfully.');
        // }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'location' => ['required'],
            'input_dbm' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $dnport = DnPorts::find($request->input('id'));
            $dnport->name = $request->name;
            $dnport->pop = $request->pop ? $request->pop['id'] : null;
            $dnport->pop_device_id = $request->pop_device_id ? $request->pop_device_id['id'] : null;
            $dnport->gpon_frame = $request->gpon_frame;
            $dnport->gpon_slot = $request->gpon_slot;
            $dnport->gpon_port = $request->gpon_port;
            $dnport->description = $request->description;
            $dnport->location = $request->location;
            $dnport->input_dbm = $request->input_dbm;
            $dnport->update();
            return redirect()->back()
                ->with('message', 'Port Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {

            $customer = Customer::join('sn_ports', 'customers.sn_id', 'sn_ports.id')
                ->join('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
                ->where('dn_ports.id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'DN cannot delete due to foreign key constraint in Customer Database.');
            DnPorts::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::join('sn_ports', 'customers.sn_id', 'sn_ports.id')
                ->join('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
                ->where('dn_ports.id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'DN cannot delete due to foreign key constraint in Customer Database.');
            DnPorts::where('name', '=', $request->input('id'))->delete();
            return redirect()->back()->with('message', 'DN Deleted Successfully.');
        }
    }
}
