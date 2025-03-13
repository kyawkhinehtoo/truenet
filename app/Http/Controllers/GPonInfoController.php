<?php

namespace App\Http\Controllers;

use App\Models\GponInfo;
use Illuminate\Http\Request;

class GPonInfoController extends Controller
{
    public function index(Request $request)
    {
        $GponInfos = DB::table('gpon_info')
            ->when($request->keyword, function ($query, $keyword) {
                $query->where('olt_name', 'LIKE', '%' . $keyword . '%');
            })
            ->paginate(10);
        $GponInfos->appends($request->all())->links();
        return Inertia::render(
            'Setup/GponInfo',
            ['GponInfos' => $GponInfos]
        );
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'olt_name' => ['required'],
            'no_of_frame' => ['required'],
        ])->validate();

        $check_dup = GponInfo::where('olt_name', $request->olt_name)
            ->exists();
        if ($check_dup) {
            return redirect()->back()->withErrors('OLT Name Already Exist!');
        } else {
            $GponInfo = new GponInfo();
            $GponInfo->olt_name = $request->olt_name;
            $GponInfo->no_of_frame = $request->no_of_frame;
            $GponInfo->save();
            return redirect()->back()->with('message', 'OLT Created Successfully.');
        }
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'site_name' => ['required'],
            'no_of_frame' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $GponInfo = GponInfo::find($request->input('id'));
            $GponInfo->site_name = $request->site_name;
            $GponInfo->site_description = $request->site_description;
            $GponInfo->site_location = $request->site_location;
            $GponInfo->update();
            if ($GponInfo->id && $request->devices) {
                GponInfoDevice::where('GponInfo_id', '=', $GponInfo->id)->delete();
                foreach ($request->devices as $device) {

                    $GponInfo_device = new GponInfoDevice();
                    $GponInfo_device->GponInfo_id = $GponInfo->id;
                    $GponInfo_device->device_name = $device['device_name'];
                    $GponInfo_device->qty = (isset($device['qty'])) ? $device['qty'] : 1;
                    $GponInfo_device->remark = (isset($device['remark'])) ? $device['remark'] : null;
                    $GponInfo_device->save();
                }
            }
            return redirect()->back()
                ->with('message', 'GponInfo Site Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            GponInfo::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'GponInfo Deleted Successfully.');
        }
    }
    public function deleteGroup(Request $request, $id)
    {
        if ($request->has('id')) {
            GponInfo::where('id', '=', $request->input('id'))->delete();
            return redirect()->back()->with('message', 'GponInfo Deleted Successfully.');
        }
    }
}