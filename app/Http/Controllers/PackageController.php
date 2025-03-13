<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Sla;
use App\Models\PackageBundle;
use App\Models\BundleEquiptment;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = Package::when($request->package, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

        $radius_services = null;
        $radius = new RadiusController();
        $radius_services =  $radius->getRadiusServices();
        if ($radius_services) {
            $radius_services = json_decode($radius_services);
        }
        $bundle_equiptments = BundleEquiptment::get();
        $slas = Sla::get();
        return Inertia::render('Setup/Package', ['packages' => $packages, 'bundle_equiptments' => $bundle_equiptments, 'slas' => $slas, 'radius_services' => $radius_services]);
    }

    public function getBundle($id)
    {
        if ($id) {
            $bundles =  DB::table('package_bundles')
                ->join('packages', 'package_bundles.package_id', '=', 'packages.id')
                ->join('bundle_equiptments', 'package_bundles.bundle_equiptment_id', '=', 'bundle_equiptments.id')
                ->where('packages.id', '=', $id)
                ->select('bundle_equiptments.id as id', 'bundle_equiptments.name as bundle_name', 'package_bundles.qty as qty', 'bundle_equiptments.detail as detail ')
                ->get();

            return response()->json($bundles);
        }
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'speed' => ['required'],
            'sla_id' => ['required'],
            'type' => ['required', 'in:ftth,b2b,dia,mpls'],
            'contract_period' => ['required', 'in:1,3,6,12,24'],
        ])->validate();
        $radius = new RadiusController();
        $radius_services =  $radius->getRadiusServices();

        $package = new Package();
        $package->name = $request->name;
        $package->speed = $request->speed;
        $package->currency = $request->currency;
        $package->type = $request->type;
        $package->status = $request->status;
        $package->sla_id = $request->sla_id;
        $package->price = $request->price;
        if ($radius_services && isset($request->radius_srvid['srvid']))
            $package->radius_package = $request->radius_srvid['srvid'];
        $package->contract_period = (string)$request->contract_period;
        $package->save();
        $id = $package->id;
        if ($request->bundleList && $id) {

            foreach ($request->bundleList as $key => $value) {
                $package_bundle = new PackageBundle();
                $package_bundle->package_id = $id;
                $package_bundle->bundle_equiptment_id = $value[0]['id'];
                $package_bundle->qty = $value[1]['qty'];
                $package_bundle->save();
            }
        }
        //  dd($request->bundle_equiptment);
        // Township::create($request->all());

        return redirect()->route('package.index')->with('message', 'Package Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required'],
            'speed' => ['required'],
            'type' => ['required', 'in:ftth,b2b,dia,mpls'],
            'sla_id' => ['required'],
            'contract_period' => ['required', 'in:1,3,6,12,24'],
        ])->validate();

        if ($request->has('id')) {

            $radius = new RadiusController();
            $radius_services =  $radius->getRadiusServices();

            $package = Package::find($request->input('id'));
            $package->name = $request->name;
            $package->speed = $request->speed;
            $package->currency = $request->currency;
            $package->type = $request->type;
            $package->sla_id = $request->sla_id;
            $package->status = $request->status;
            $package->price = $request->price;
            if ($radius_services && isset($request->radius_srvid['srvid']))
                $package->radius_package = $request->radius_srvid['srvid'];
            $package->contract_period = (string)$request->contract_period;
            $package->update();
            PackageBundle::where('package_id', $request->input('id'))->delete();
            if ($request->bundleList) {
                foreach ($request->bundleList as $key => $value) {
                    $package_bundle = new PackageBundle();
                    $package_bundle->package_id = $request->input('id');
                    $package_bundle->bundle_equiptment_id = $value[0]['id'];
                    $package_bundle->qty = $value[1]['qty'];
                    $package_bundle->save();
                }
            }
            return redirect()->back()
                ->with('message', 'Package Updated Successfully.');
        }
    }
    public function getPackage(Request $request)
    {
        $sn = null;
        if ($request->id) {

            $sn = DB::table('packages')
                ->where('packages.pop_id', '=', $request->id)
                ->select('packages.*')
                ->get();
        }
        return response()
            ->json($sn, 200);
    }
    public function getIdByName(Request $request) {}
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('package_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Package cannot delete due to foreign key constraint in Customer Database.');
            Package::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Package deleted successfully');
        }
    }
}
