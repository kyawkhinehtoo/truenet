<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Township;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class TownshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $data = Township::all();
        // return Inertia::render('township', ['data' => $data]);
        $cities = City::all();
        $townships = Township::leftjoin('cities', 'cities.id', '=', 'townships.city_id')
            ->when($request->township, function ($query, $tsp) {
                $query->where('townships.name', 'LIKE', '%' . $tsp . '%')
                    ->orWhere('townships.short_code', 'LIKE', '%' . $tsp . '%');
            })
            ->select('townships.*', 'cities.name as city_name')
            ->paginate(10);

        return Inertia::render('Setup/Township', ['townships' => $townships, 'cities' => $cities]);
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
            'city_id' => ['required'],
            'short_code' => ['required'],
        ])->validate();
        $township = new Township();
        $township->name = $request->name;
        $township->city_id = $request->city_id['id'];
        $township->short_code = $request->short_code;
        $township->save();

        return redirect()->route('township.index')->with('message', 'Township Created Successfully.');
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
            'city_id' => ['required'],
            'short_code' => ['required'],
        ])->validate();

        if ($request->has('id')) {

            $township = Township::find($request->input('id'));
            $township->name = $request->name;
            $township->city_id = $request->city_id['id'];
            $township->short_code = $request->short_code;
            $township->update();
            return redirect()->back()
                ->with('message', 'Township Updated Successfully.');
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
            $customer = Customer::where('township_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Township cannot delete due to foreign key constraint in Customer Database.');
            Township::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Township deleted successfully.');
        }
    }
}
