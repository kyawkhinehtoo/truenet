<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Subcom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class SubcomController extends Controller
{
    public function index(Request $request)
    {
        $subcom = Subcom::when($request->subcom, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
            ->paginate(10);
        return Inertia::render('Setup/Subcom', ['subcoms' => $subcom]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'rate' => ['required'],
            'contact_person' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
        ])->validate();

        $subcom = new Subcom();
        $subcom->name = $request->name;
        $subcom->contact_person = $request->contact_person;
        $subcom->email = $request->email;
        $subcom->phone = $request->phone;
        $subcom->disabled = $request->disabled;
        $subcom->rate = json_encode($request->rate);
        $subcom->save();
        return redirect()->route('subcom.index')->with('message', 'Subcom Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'rate' => ['required'],
            'contact_person' => ['required'],
            'email' => ['required'],
            'phone' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $subcom = Subcom::find($request->input('id'));
            $subcom->name = $request->name;
            $subcom->contact_person = $request->contact_person;
            $subcom->email = $request->email;
            $subcom->phone = $request->phone;
            $subcom->disabled = $request->disabled;
            $subcom->rate = json_encode($request->rate);
            $subcom->update();
            return redirect()->back()
                ->with('message', 'Subcom Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('subcom_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Subcom cannot delete due to foreign key constraint in Customer Database.');
            Subcom::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Subcom deleted successfully.');
        }
    }
}
