<?php

namespace App\Exports;

use App\Http\Controllers\RadiusController;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\SnPorts;
use App\Models\User;
use App\Models\Status;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class RadiusExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function __construct(Request $request)
    {
        set_time_limit(300);
        $this->request = $request;
    }
    public function query()
    {
        $request = $this->request;
       
        $expiration_from = (isset($request->expiration[0])) ? $request->expiration[0] . ' 00:00:00' : '2000-01-01 00:00:00';
        $expiration_to = (isset($request->expiration[1])) ? $request->expiration[1] . ' 23:59:59' : '2100-01-01 00:00:00';
        $expiration = array('from' => $expiration_from, 'to' => $expiration_to);
        $radius_users = null;

        $radius_controller = new RadiusController();
        if ($request->radius_status == 'online') {
            $radius_users = $radius_controller->getOnlineUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'offline') {
            $radius_users = $radius_controller->getOfflineUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'disabled') {
            $radius_users = $radius_controller->getDisabledUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'not found') {
            $radius_users = $radius_controller->getRadiusUser();
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'expired') {
            $radius_users = $radius_controller->getExpiredUser($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        if ($request->radius_status == 'range_expired') {
            $radius_users = $radius_controller->getExpiredRange($expiration);
            $radius_users = json_decode($radius_users, true);
        }
        $mycustomer =  DB::table('customers')
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
                if ($radius_users) {
                    $online_users = array();
                    foreach ($radius_users as $user) {
                        array_push($online_users, $user['username']);
                    }
                    if ($radius_status == 'online') {
                        return  $query->whereIn('customers.pppoe_account', $online_users);
                    } elseif ($radius_status == 'offline') {
                        return $query->whereIn('customers.pppoe_account', $online_users);
                    } elseif ($radius_status == 'disabled') {
                        return $query->whereIn('customers.pppoe_account', $online_users);
                    } elseif ($radius_status == 'expired') {
                        return $query->whereIn('customers.pppoe_account', $online_users);
                    } elseif ($radius_status == 'range_expired') {
                        return $query->whereIn('customers.pppoe_account', $online_users);
                    } elseif ($radius_status == 'not found') {
                        return $query->whereNotIn('customers.pppoe_account', $online_users);
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
            ->select('customers.*');
        return $mycustomer;
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'NRC',
            'Phone 1',
            'Phone 2',
            'Address',
            'Lat Long',
            'Township',
            'Package',
            'Bandwidth',
            'Extra Bandwidth',
            'Contract',
            'Installation Team',
            'Sale Person',
            'Sale Source',
            'Sale Remark',
            'Order Date',
            'Prefer Install Date',
            'Installation Date',
            'Installation Remark',
            'Payment Type',
            'Prepaid Period',
            'Fiber Distance',
            'ONU Serial',
            'ONU Power',
            'DN',
            'SN',
            'Status',
            'Radius Status',
            'Radius Expiration',

        ];
    }

    public function map($mycustomer): array
    {
        $township = Township::find($mycustomer->township_id);
        $package = Package::find($mycustomer->package_id);
        $subcom = User::find($mycustomer->subcom_id);
        $status = Status::find($mycustomer->status_id);
        $sale_person = User::find($mycustomer->sale_person_id);
        if (isset($mycustomer->sn_id)) {
            $sn_dn = DB::table('sn_ports')
                ->join('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
                ->join('customers', 'customers.sn_id', '=', 'sn_ports.id')
                ->where('customers.sn_id', '=', $mycustomer->sn_id)
                ->select('dn_ports.name as dn_name', 'sn_ports.name as sn_name')
                ->first();
        }
        $radius = RadiusController::checkRadiusEnable();
        if ($radius) {

            if ($mycustomer->pppoe_account) {
                $mycustomer->radius_status = RadiusController::checkCustomer($mycustomer->pppoe_account);
                $radius_controller = new RadiusController();
                $radius_info = $radius_controller->getRadiusInfo($mycustomer->id);

                $mycustomer->expiration = (isset($radius_info[0])) ? Carbon::parse($radius_info[0]->expiration, 'UTC')->setTimezone('Asia/Yangon')->format('d-M-Y H:i:s') : '';
            } else {
                $mycustomer->radius_status = 'no account';
            }
        }

        return [
            $mycustomer->ftth_id,
            $mycustomer->name,
            $mycustomer->nrc,
            $mycustomer->phone_1,
            $mycustomer->phone_2,
            $mycustomer->address,
            $mycustomer->location,
            (isset($township->name)) ? $township->name : '',
            (isset($package->name)) ? $package->name : '',
            (isset($package->speed)) ? $package->speed . ' Mbps' : '',
            ($mycustomer->extra_bandwidth) ? $mycustomer->extra_bandwidth . ' Mbps' : '',
            (isset($package->contract_period)) ? $package->contract_period . ' Months' : '',
            (isset($subcom->name)) ? $subcom->name : '',
            (isset($sale_person->name)) ? $sale_person->name : '',
            $mycustomer->sale_channel,
            $mycustomer->sale_remark,
            $mycustomer->order_date,
            $mycustomer->prefer_install_date,
            $mycustomer->installation_date,
            $mycustomer->installation_remark,
            ($mycustomer->advance_payment == 0) ? "Postpaid" : "Prepaid",
            $mycustomer->advance_payment . ' Months -' . $mycustomer->advance_payment_day . ' Days',
            $mycustomer->fiber_distance,
            $mycustomer->onu_serial,
            $mycustomer->onu_power,
            (isset($sn_dn)) ? $sn_dn->dn_name : "",
            (isset($sn_dn)) ? $sn_dn->sn_name : "",
            $status->name,
            (isset($mycustomer->radius_status)) ? $mycustomer->radius_status : "",
            (isset($mycustomer->expiration)) ? $mycustomer->expiration : "",

        ];
    }
}
