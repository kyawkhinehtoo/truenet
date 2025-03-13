<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Township;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cities = City::when($request->city, function ($query, $city) {
            $query->where('name', 'LIKE', '%' . $city . '%')
                ->orWhere('short_code', 'LIKE', '%' . $city . '%');
        })
            ->paginate(10);

        return Inertia::render('Setup/City', ['cities' => $cities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'short_code' => ['required'],
        ])->validate();

        City::create($request->all());

        return redirect()->route('city.index')->with('message', 'City Created Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function show(City $cityModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\City  $cityModel
     * @return \Illuminate\Http\Response
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\City  $cityModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, City $city)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'short_code' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            City::find($request->input('id'))->update($request->all());
            return redirect()->back()
                ->with('message', 'City Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\City  $city
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $township = Township::where('city_id', '=', $request->input('id'))->first();
            if ($township)
                return redirect()->back()->with('message', 'City cannot delete due to foreign key constraint in Township Database.');
            City::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'City deleted successfully.');
        }
    }
}
