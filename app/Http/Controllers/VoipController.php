<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voip;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;
class VoipController extends Controller
{
    public function index(Request $request)
    {
         $voips = Voip::when($request->voip, function($query, $pkg){
             $query->where('voip_number','LIKE','%'.$pkg.'%');
         })
         ->paginate(10);
        return Inertia::render('Setup/Voip', ['voips' => $voips]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'voip_number' => ['required'],
        ])->validate();

        $voip = new Voip();
        $voip->voip_number = $request->voip_number;
        $voip->save();
         return redirect()->route('voip.index')->with('message', 'Voip Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'voip_number' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $voip = Voip::find($request->input('id'));
            $voip->voip_number = $request->voip_number;
            $voip->update();
            return redirect()->back()
                    ->with('message', 'Voip Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Voip::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
