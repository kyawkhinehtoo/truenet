<?php

namespace App\Http\Controllers;

use App\Models\BillingConfig;
use Illuminate\Http\Request;
use App\Models\RadiusConfig;
use App\Models\Customer;
use App\Models\DnPorts;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Township;
use App\Models\Role;
use Carbon\Carbon;
use DateTime;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RadiusController extends Controller
{
    public function index(Request $request)
    {
        $config = RadiusConfig::all();
        return Inertia::render('Setup/RadiusConfig', ['config' => $config]);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'server_url' => ['required'],
            'port' => ['required'],
            'admin_user' => ['required'],
            'admin_password' => ['required'],
            'enabled' => ['required'],
        ])->validate();

        $config = new RadiusConfig();
        $config->server = $request->server_url;
        $config->admin_user = $request->admin_user;
        $config->admin_password = $request->admin_password;
        $config->port = (int)$request->port;
        $config->enabled = (int)$request->enabled;
        $config->save();
        return redirect()->back()->with('message', 'Config Created Successfully.');
    }
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'server_url' => ['required'],
            'port' => ['required'],
            'admin_user' => ['required'],
            'admin_password' => ['required'],
            'enabled' => ['required'],
        ])->validate();

        if ($request->has('id')) {
            $config =  RadiusConfig::find($request->input('id'));
            $config->server = $request->server_url;
            $config->admin_user = $request->admin_user;
            $config->admin_password = $request->admin_password;
            $config->port = (int)$request->port;
            $config->enabled = (int)$request->enabled;
            $config->update();
            return redirect()->back()
                ->with('message', 'Config Updated Successfully.');
        }
    }

    public static function loginRadius()
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $credential['name'] = $radius_config->admin_user;
            $credential['password'] = $radius_config->admin_password;
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/login';
            $auth_check = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/auth-api';
            $client = new \GuzzleHttp\Client();
            if (session('token')) {
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = "";
                try {
                    $res = $client->post($auth_check, ['headers' => $header], ['connect_timeout' => 1]);
                } catch (\Throwable $th) {
                    $res = $client->post($url, ['form_params' => $credential], ['connect_timeout' => 1]);
                    if ($res->getStatusCode() == 200) {
                        $data = json_decode($res->getBody(), true);
                        session(['token' => $data['token']]);
                    }
                }
            } else {
                $res = $client->post($url, ['form_params' => $credential], ['connect_timeout' => 1]);
                if ($res->getStatusCode() == 200) {
                    $data = json_decode($res->getBody(), true);
                    session(['token' => $data['token']]);
                }
            }
        }
    }

    public static function checkCustomer($pppoe_account)
    {

        if (self::checkRadiusEnable()) {
            $radius_config = RadiusConfig::first();
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/check-online';
            $data['pppoe_account'] = $pppoe_account;
            $response = null;
            try {

                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return $response->message;
                }
            } catch (\Throwable $e) {
                return 'no account';
            }
        }
    }

    public function autofillRadius()
    {
        $customers =  Customer::join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('users', 'customers.sale_person_id', '=', 'users.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.pppoe_account')
            ->get();
        foreach ($customers as $customer) {

            if (!$customer->pppoe_account) {
                // if ($customer->ftth_id && $customer->township && $customer->project) {
                //     $pppoe = substr(strtolower($customer->ftth_id), 0, 5) . '_' . strtolower($customer->township) . '_' . strtolower($customer->project);
                //     Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($pppoe)]);
                //     echo $customer->ftth_id . " PPPOE : " . $pppoe . " updated : <br />";
                // }
                Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($customer->ftth_id)]);
            }
            if (!$customer->pppoe_password) {
                if ($customer->ftth_id && $customer->township && $customer->project) {
                    // Customer::where('id', $customer->id)->update(['pppoe_password' => 'margaaftth']);
                }
            }
        }
    }
    public function autofillPrefer()
    {
        $customers =  Customer::join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNull('customers.prefer_install_date')
            ->whereNotNull('customers.installation_date')
            ->get();
        foreach ($customers as $customer) {

            if (!$customer->prefer_install_date) {

                Customer::where('id', $customer->id)->update(['pppoe_account' => strtolower($customer->pppoe)]);
                echo $customer->ftth_id . " updated : <br />";
            }
        }
    }
    public function getRadiusInfo($id)
    {
        if ($id) {
            $customer = Customer::find($id);
            if ($customer->pppoe_account) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-account';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return $response->data;
                        }
                    } catch (\Throwable $e) {
                        return 'no account';
                    }
                }
            }
        }
    }
    public static function checkRadiusEnable()
    {
        $radius_config = RadiusConfig::first();
        if ($radius_config) {
            if ($radius_config->enabled) {
                return true;
            }
        }
        return false;
    }
    public function saveRadius(Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'required|max:255',
            'status' => 'required|max:255',
            'srv' => 'required',
        ])->validate();
        $customer = Customer::find($request->id);
        if ($customer->pppoe_account) {
            $radius_config = RadiusConfig::first();
            if (self::checkRadiusEnable()) {
                $client = new \GuzzleHttp\Client();
                $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/data';
                $data['username'] = $customer->pppoe_account;
                $data['srvid'] = $request->srv['srvid'];
                $data['enableuser'] = ($request->status == true) ? 1 : 0;
                $data['expiration'] = $request->expiration;
                $response = null;
                try {
                    $this->loginRadius();
                    $header = ['Authorization' => 'Bearer ' . session('token')];
                    $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                    $response = json_decode($res->getBody());
                    if ($response) {
                        return redirect()->back()
                            ->with('message', 'Updated Radius Information Successfully.');
                    }
                } catch (\Throwable $e) {
                    return redirect()->back()
                        ->with('message', 'Updated Radius Information Fail.');
                }
            }
        }
    }
    public function enableRadiusUser(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/enable';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                ->with('message', 'Updated Radius Information Successfully.');
                        }
                    } catch (\Throwable $e) {
                        return redirect()->back()
                            ->with('message', 'Updated Radius Information Fail.');
                    }
                }
            }
        }
    }
    public function disableRadiusUser(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/disable';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                ->with('message', 'Disabled User Successfully.');
                        }
                    } catch (\Throwable $e) {
                        return redirect()->back()
                            ->with('message', 'Disabled User Fail.');
                    }
                }
            }
        }
    }

    public function getRadiusServices()
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-services';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return 'no account';
            }
        }
    }
    public function getOnlineUser($expiration)
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-online';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getOfflineUser($expiration)
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-offline';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getDisabledUser($expiration)
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-disabled';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getExpiredUser($expiration)
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-expiry';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getActiveUser()
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-active';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getExpiredRange($expiration)
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-expiry-all';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public static function getExpiredAll($month, $year)
    {
        $expiration_from = '2000-01-01 00:00:00';
        // Create a DateTime object set to the first day of the next month
        $nextMonth = new DateTime("$year-$month-01");
        $nextMonth->modify('first day of next month')->setTime(0, 0, 0);
        $expiration_to = $nextMonth->format('Y-m-d H:i:s');
        $expiration = array('from' => $expiration_from, 'to' => $expiration_to);
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-expiry-all';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header, 'form_params' => $expiration], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());

                if ($response) {
                    return json_encode($response->data, 200);
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public function getRadiusUser()
    {
        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/get-all-account';
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $res = $client->post($url, ['headers' => $header], ['connect_timeout' => 1]);
                $response = json_decode($res->getBody());
                if ($response) {
                    return $response->data; // Remove json_encode since the data is already in the correct format
                }
            } catch (\Throwable $e) {
                return null;
            }
        }
        return null;
    }
    public static function createRadius($customer_id)
    {

        $data = DB::table('customers')
            ->join('townships', 'customers.township_id', 'townships.id')
            ->join('cities', 'townships.city_id', 'cities.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->join('status', 'customers.status_id', 'status.id')
            ->leftjoin('sn_ports', 'customers.sn_id', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
            ->where('customers.id', '=', $customer_id)
            ->select('customers.*', 'townships.name as township_name', 'status.type as status_type', 'cities.name as city', 'dn_ports.name as dn_name', 'sn_ports.name as sn_name', 'packages.radius_package as srvid')
            ->first();
        $billconfig = BillingConfig::first();
        if (isset($data->pppoe_account) && isset($data->pppoe_password)) {

            $dn = ($data->dn_name) ? $data->dn_name : null;
            $sn = ($data->sn_name) ? '/' . $data->sn_name : null;

            $user_data['username'] = $data->pppoe_account;
            $user_data['password'] = $data->pppoe_password;
            $user_data['groupid'] = 1; //can be anything
            $user_data['enableuser'] = ($data->status_type == 'active') ? 1 : 0;
            $user_data['uplimit'] = 0;
            $user_data['downlimit'] = 0;
            $user_data['comblimit'] = 0;
            $user_data['firstname'] = $data->name;
            $user_data['lastname'] = ($dn && $sn) ? $dn . $sn : '';
            $user_data['company'] = ($data->onu_serial) ? $data->onu_serial : '';
            $user_data['phone'] = $data->phone_1;
            $user_data['mobile'] = ($data->social_account) ? $data->social_account : '';
            $user_data['address'] = $data->address;
            $user_data['city'] = $data->township_name;
            $user_data['country'] = $data->city;
            $location = explode(",", $data->location);
            $user_data['gpslat'] = (isset($location[0])) ? $location[0] : '0.0';
            $user_data['gpslong'] = (isset($location[1])) ?  $location[1] : '0.0';
            $user_data['usemacauth'] = 0;
            $today = new DateTime('now');
            // $today->modify('last day of this month');
            $today->modify('+3  day');
            $today->setTime(23, 59, 0);
            $user_data['expiration'] = ($data->service_off_date) ? $data->service_off_date : $today;
            $user_data['uptimelimit'] = 0;
            $user_data['srvid'] = 0;
            $user_data['ipmodecm'] = 0;
            $user_data['ipmodecpe'] = 0;
            $user_data['poolidcm'] = 0;
            $user_data['poolidcpe'] = 0;
            $user_data['createdon'] = date('Y-m-d H:i:s');
            $user_data['acctype'] = 0;
            $user_data['credits'] = 0.00;
            $user_data['cardfails'] = 0;
            $user_data['createdby'] = 'adminapi';
            $user_data['owner'] = 'adminapi';
            $user_data['email'] = '';
            $user_data['warningsent'] = 0;
            $user_data['verified'] = 0;
            $user_data['selfreg'] = 0;
            $user_data['verifyfails'] = 0;
            $user_data['verifysentnum'] = 0;
            $user_data['contractvalid'] = null;
            $user_data['pswactsmsnum'] = 0;
            $user_data['alertemail'] = 1;
            $user_data['alertsms'] = 1;
            $user_data['zip'] = '';
            $user_data['lang'] = 'English';
            if ($data->srvid)
                $user_data['srvid'] = $data->srvid;

            $radius_config = RadiusConfig::first();
            if (self::checkRadiusEnable()) {
                $client = new \GuzzleHttp\Client();
                $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/create';
                $response = null;
                try {
                    self::loginRadius();
                    $header = ['Authorization' => 'Bearer ' . session('token')];
                    $res = $client->post($url, ['headers' => $header, 'form_params' => $user_data], ['connect_timeout' => 1]);
                    $response = json_decode($res->getBody());
                    if ($response) {
                        return redirect()->back()
                            ->with('message', 'Created User Successfully.');
                    }
                } catch (\Throwable $e) {
                    return redirect()->back()
                        ->with('message', 'Create User Fail.');
                }
            }
        }
    }
    public static function updateRadius($id)
    {

        $data = DB::table('customers')
            ->join('townships', 'customers.township_id', 'townships.id')
            ->join('cities', 'townships.city_id', 'cities.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->join('status', 'customers.status_id', 'status.id')
            ->leftjoin('sn_ports', 'customers.sn_id', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', 'dn_ports.id')
            ->where('customers.id', '=', $id)
            ->select('customers.*', 'townships.name as township_name', 'status.name as status_name', 'status.type as status_type', 'cities.name as city', 'dn_ports.name as dn_name', 'sn_ports.name as sn_name', 'packages.radius_package as srvid')
            ->first();

        if (isset($data->pppoe_account)) {

            $dn = ($data->dn_name) ? $data->dn_name : null;
            $sn = ($data->sn_name) ? '/' . $data->sn_name : null;
            $user_data['username'] = $data->pppoe_account;

            if (isset($data->pppoe_password))
                $user_data['password'] = $data->pppoe_password;

            $user_data['groupid'] = 1; //default HTI

            // switch ($data->project_id) {
            //     case 4:
            //         //Complex45
            //         $user_data['groupid'] = 6;
            //         break;
            //     case 2:
            //         //Gonyi
            //         $user_data['groupid'] = 3;
            //         break;
            //     case 6:
            //             //HTI
            //         $user_data['groupid'] = 7;
            //         break;
            //     case 7:
            //         //Netcore
            //         $user_data['groupid'] = 8;
            //         break;
            // }
            //$user_data['groupid'] = 8;

            if ($data->status_name != 'Expired')
                $user_data['enableuser'] = ($data->status_type == 'active') ? 1 : 0;

            $user_data['firstname'] = $data->name;
            $user_data['lastname'] = ($dn && $sn) ? $dn . $sn : '';
            $user_data['company'] = ($data->onu_serial) ? $data->onu_serial : '';
            $user_data['phone'] = $data->phone_1;
            $user_data['mobile'] = ($data->phone_2) ? $data->phone_2 : '';
            $user_data['address'] = $data->address;
            $user_data['city'] = $data->township_name;
            $user_data['country'] = $data->city;
            $location = explode(",", $data->location);
            $user_data['gpslat'] = (isset($location[0])) ? $location[0] : '0.0';
            $user_data['gpslong'] = (isset($location[1])) ?  $location[1] : '0.0';
            $user_data['zip'] = '';
            $user_data['email'] = '';

            //Package
            if ($data->srvid)
                $user_data['srvid'] = $data->srvid;

            $radius_config = RadiusConfig::first();
            if (self::checkRadiusEnable()) {
                $client = new \GuzzleHttp\Client();
                $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/update';
                $response = null;
                try {
                    self::loginRadius();
                    $header = ['Authorization' => 'Bearer ' . session('token')];
                    $res = $client->post($url, ['headers' => $header, 'form_params' => $user_data], ['connect_timeout' => 1]);
                    $response = json_decode($res->getBody());
                    if ($response) {
                        return redirect()->back()
                            ->with('message', 'Created User Successfully.');
                    }
                } catch (\Throwable $e) {
                    return redirect()->back()
                        ->with('message', 'Create User Fail.');
                }
            }
        }
    }

    public function display(Request $request)
    {



        $expiration_from = (isset($request->expiration[0])) ? $request->expiration[0] . ' 00:00:00' : '2000-01-01 00:00:00';
        $expiration_to = (isset($request->expiration[0])) ? $request->expiration[1] . ' 23:59:59' : '2100-01-01 00:00:00';
        $expiration = array('from' => $expiration_from, 'to' => $expiration_to);
        $radius_users = null;
        if ($request->radius_status == 'online') {
            $radius_users = RadiusController::getOnlineUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'offline') {
            $radius_users = RadiusController::getOfflineUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'disabled') {
            $radius_users = RadiusController::getDisabledUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'not found') {
            $radius_users = RadiusController::getRadiusUser();
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'expired') {
            $radius_users = RadiusController::getExpiredUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'range_expired') {
            $radius_users = RadiusController::getExpiredRange($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        $vlans = Customer::where(function ($query) {
            return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
        })->select('vlan')
            ->groupBy('vlan')
            ->get();
        $customers =  DB::table('customers')
            ->leftjoin('packages', 'customers.package_id', '=', 'packages.id')
            ->leftjoin('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->leftjoin('sn_ports', 'customers.sn_id', '=', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($request->vlan, function ($query, $vlan) {
                $query->where('customers.vlan', '=',  $vlan);
            })
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->radius_status, function ($query, $radius_status) use ($radius_users) {

                if ($radius_status == 'no account') {
                    return $query->whereNull('customers.pppoe_account');
                }
                // dd($radius_users);
                if ($radius_users) {
                    $user_result = array();
                    foreach ($radius_users as $user) {
                        array_push($user_result, $user['username']);
                    }
                    if ($radius_status == 'online') {
                        return $query->whereIn('customers.pppoe_account', $user_result);
                    } elseif ($radius_status == 'offline') {
                        return $query->whereIn('customers.pppoe_account', $user_result);
                    } elseif ($radius_status == 'disabled') {
                        return $query->whereIn('customers.pppoe_account', $user_result);
                    } elseif ($radius_status == 'expired') {
                        return $query->whereIn('customers.pppoe_account', $user_result);
                    } elseif ($radius_status == 'range_expired') {
                        return $query->whereIn('customers.pppoe_account', $user_result);
                    } elseif ($radius_status == 'not found') {
                        return $query->whereNotIn('customers.pppoe_account', $user_result);
                    }
                } else {
                    if ($radius_status != 'any') {
                        return $query->where('customers.pppoe_account', '=', '');
                    }
                }
            }, function ($query) use ($radius_users) {
                if ($radius_users) {
                    $user_result = array();
                    foreach ($radius_users as $user) {
                        array_push($user_result, $user['username']);
                    }
                    return  $query->whereIn('customers.pppoe_account', $user_result);
                }
            })

            ->orderBy('customers.id', 'desc')
            ->select('customers.id as id', 'customers.ftth_id as ftth_id', 'customers.name as name', 'customers.prefer_install_date as prefer_install_date', 'customers.order_date as order_date', 'customers.phone_1 as phone', 'townships.name as township', 'packages.name as package', 'status.name as status', 'status.color as color', 'customers.pppoe_account as pppoe_account')
            ->paginate(10);
        $radius = RadiusController::checkRadiusEnable();
        if ($radius) {
            foreach ($customers as $key => $value) {
                if ($value->pppoe_account) {
                    $value->radius_status = RadiusController::checkCustomer($value->pppoe_account);
                    $radius_info = RadiusController::getRadiusInfo($value->id);

                    //  $value->expiration = (isset($radius_info[0]))?$radius_info[0]->expiration:'';
                    $value->expiration = (isset($radius_info[0])) ? Carbon::parse($radius_info[0]->expiration, 'UTC')->setTimezone('Asia/Yangon')->format('d-M-Y H:i:s') : '';
                } else {
                    $value->radius_status = 'no account';
                }
            }
        }
        // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render('Client/Radius', [
            'customers' => $customers,
            'radius' => $radius,
            'vlans' => $vlans
        ]);
    }
    public static function setExpiry($username, $expiration)
    {

        $radius_config = RadiusConfig::first();
        if (self::checkRadiusEnable()) {
            $client = new \GuzzleHttp\Client();
            $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/set-expiry';
            $data['username'] = $username;
            $data['expiration'] = $expiration;
            $response = null;
            try {
                self::loginRadius();
                $header = ['Authorization' => 'Bearer ' . session('token')];
                $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                //  $response = json_decode($res->getBody());

            } catch (\Throwable $e) {
                //  return redirect()->back()
                //        ->with('message', 'Updated Radius Information Fail.');
                dd($e);
            }
        }
    }
    public function tempDeactivate(Request $request)
    {

        if ($request->id) {
            $customer = Customer::find($request->id);
            $status = Status::where('status.type', '=', 'disabled')->first();
            if ($status) {
                Customer::where('id', '=', $request->id)->update(['status_id' => $status->id]);
            }
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/disable';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {


                            return redirect()->back()
                                ->with('message', 'Disabled User Successfully.');
                        }
                    } catch (\Throwable $e) {
                        return redirect()->back()
                            ->with('message', 'Disabled User Fail.');
                    }
                }
            } else {
                return redirect()->back()
                    ->with('message', 'Updated Radius Information Fail.');
            }
        }
    }
    public function tempActivate(Request $request)
    {
        if ($request->id) {
            $customer = Customer::find($request->id);
            $status = Status::where('status.type', '=', 'active')->first();
            if ($status) {
                Customer::where('id', '=', $request->id)->update(['status_id' => $status->id]);
            }
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/enable';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        $this->loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {

                            return redirect()->back()
                                ->with('message', 'Updated Radius Information Successfully.');
                        }
                    } catch (\Throwable $e) {
                        return redirect()->back()
                            ->with('message', 'Updated Radius Information Fail.');
                    }
                }
            } else {
                return redirect()->back()
                    ->with('message', 'Updated Radius Information Fail.');
            }
        }
    }
    public static function deleteUser($customer_id)
    {
        if ($customer_id) {
            $customer = Customer::find($customer_id);
            if (isset($customer->pppoe_account)) {
                $radius_config = RadiusConfig::first();
                if (self::checkRadiusEnable()) {
                    $client = new \GuzzleHttp\Client();
                    $url = 'http://' . $radius_config->server . ':' . $radius_config->port . '/api/delete';
                    $data['pppoe_account'] = $customer->pppoe_account;
                    $response = null;
                    try {
                        self::loginRadius();
                        $header = ['Authorization' => 'Bearer ' . session('token')];
                        $res = $client->post($url, ['headers' => $header, 'form_params' => $data], ['connect_timeout' => 1]);
                        $response = json_decode($res->getBody());
                        if ($response) {
                            return redirect()->back()
                                ->with('message', 'Delete Radius User Successfully.');
                        }
                    } catch (\Throwable $e) {
                        return redirect()->back()
                            ->with('message', 'Delete Radius User Fail.');
                    }
                }
            } else {
                return redirect()->back()
                    ->with('message', 'Delete Radius User Fail.');
            }
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            RadiusConfig::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
}
