<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BundleEquiptment;
use App\Models\Customer;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class EquiptmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $data = BundleEquiptment::all();
        // return Inertia::render('BundleEquiptment', ['data' => $data]);

        $equiptments = BundleEquiptment::when($request->equiptment, function ($query, $tsp) {
            $query->where('name', 'LIKE', '%' . $tsp . '%')
                ->orWhere('detail', 'LIKE', '%' . $tsp . '%');
        })
            ->paginate(10);
        return Inertia::render('Setup/BundleEquiptment', ['equiptments' => $equiptments]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'name' => ['required'],
            'detail' => ['required'],
        ])->validate();

        BundleEquiptment::create($request->all());

        return redirect()->route('equiptment.index')->with('message', 'BundleEquiptment Created Successfully.');
        // return redirect()->route('posts.index') 
        // ->with('message', 'Post Created Successfully.');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'detail' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            BundleEquiptment::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'BundleEquiptment Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::whereRaw('FIND_IN_SET(?, bundle)', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'BundleEquiptment cannot delete due to foreign key constraint in Customer Database.');
            BundleEquiptment::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'BundleEquiptment deleted successfully.');
        }
    }
}
