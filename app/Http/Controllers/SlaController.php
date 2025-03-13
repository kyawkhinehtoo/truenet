<?php

namespace App\Http\Controllers;
use App\Models\Sla;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
class SlaController extends Controller
{
    public function index(Request $request)
    {
         $slas = Sla::when($request->sla, function($query, $pkg){
             $query->where('percentage','LIKE','%'.$pkg.'%');
         })
         ->paginate(10);
        return Inertia::render('Setup/Sla', ['slas' => $slas]);

    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'percentage' => ['required'],
        ])->validate();

        $sla = new Sla();
        $sla->percentage = $request->percentage;
        $sla->save();
         return redirect()->route('sla.index')->with('message', 'Sla Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'percentage' => ['required'],
        ])->validate();
  
        if ($request->has('id')) {
            $sla = Sla::find($request->input('id'));
            $sla->percentage = $request->percentage;
            $sla->update();
            return redirect()->back()
                    ->with('message', 'Sla Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Sla::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
