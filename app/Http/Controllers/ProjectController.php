<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::when($request->project, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
            ->paginate(10);
        return Inertia::render('Setup/Project', ['projects' => $projects]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        $project = new Project();
        $project->name = $request->name;
        $project->save();
        return redirect()->route('project.index')->with('message', 'Project Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $project = Project::find($request->input('id'));
            $project->name = $request->name;
            $project->update();
            return redirect()->back()
                ->with('message', 'Project Updated Successfully.');
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('project_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Project cannot delete due to foreign key constraint in Customer Database.');
            Project::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Project deleted successfully');
        }
    }
}
