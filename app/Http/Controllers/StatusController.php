<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Status;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;

class StatusController extends Controller
{
    public function index(Request $request)
    {
        $status = Status::when($request->status, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
            ->paginate(10);
        return Inertia::render('Setup/Status', ['status' => $status]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'color' => ['required'],
            'type' => ['required'],
        ])->validate();

        $status = new Status();
        $status->name = $request->name;
        $status->color = $request->color;
        $status->start_date = $request->start_date;
        $status->end_date = $request->end_date;
        $status->type = $request->type;
        $status->save();
        return redirect()->route('status.index')->with('message', 'Status Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'color' => ['required'],
            'type' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $status = Status::find($request->input('id'));
            $status->name = $request->name;
            $status->color = $request->color;
            $status->start_date = $request->start_date;
            $status->end_date = $request->end_date;
            $status->type = $request->type;
            $status->update();
            return redirect()->back()
                ->with('message', 'Status Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('status_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Status cannot delete due to foreign key constraint in Customer Database.');
            Status::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Status successfully deleted.');
        }
    }
}
