<?php

namespace App\Exports;

use App\Models\BundleEquiptment;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\SnPorts;
use App\Models\User;
use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as StyleNumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class CustomersExport implements FromQuery, WithColumnFormatting, WithMapping, WithHeadings, WithStrictNullComparison
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function query()
    {
        $request = $this->request;

        $packages = Package::get();
        $townships = Township::get();
        $projects = Project::get();
        $status = Status::get();



        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

        $all_township = Township::select('id')
            ->get()
            ->toArray();
        $all_packages = Package::select('id')
            ->get()
            ->toArray();

        $mycustomer =  DB::table('customers')
            ->leftjoin('packages', 'customers.package_id', '=', 'packages.id')
            ->leftjoin('townships', 'customers.township_id', '=', 'townships.id')
            ->leftjoin('users', 'customers.sale_person_id', '=', 'users.id')
            ->leftjoin('projects', 'customers.project_id', '=', 'projects.id')
            ->leftjoin('sn_ports', 'customers.sn_id', '=', 'sn_ports.id')
            ->leftjoin('dn_ports', 'sn_ports.dn_id', '=', 'dn_ports.id')
            ->leftjoin('pops', 'dn_ports.pop', '=', 'pops.id')
            ->leftjoin('pop_devices', 'dn_ports.pop_device_id', '=', 'pop_devices.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
                });
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->order, function ($query, $order) {
                $startDate = Carbon::parse($order[0])->format('Y-m-d');
                $endDate = Carbon::parse($order[1])->format('Y-m-d');
                $query->whereBetween('customers.order_date',  [$startDate, $endDate]);
            })
            ->when($request->prefer, function ($query, $prefer) {
                $startDate = Carbon::parse($prefer[0])->format('Y-m-d');
                $endDate = Carbon::parse($prefer[1])->format('Y-m-d');
                $query->whereBetween('customers.prefer_install_date', [$startDate, $endDate]);
            })
            ->when($request->dn, function ($query, $dn) {
                $query->where('dn_ports.id', '=', $dn);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('sn_ports.id', '=', $sn);
            })
            ->when($request->package, function ($query, $package) use ($all_packages) {
                if ($package == 'empty') {
                    $query->whereNotIn('customers.package_id', $all_packages);
                } else {
                    $query->where('customers.package_id', '=', $package);
                }
            })
            ->when($request->package_speed, function ($query, $package_speed) {
                $speed_type =  explode("|", $package_speed);
                $speed = $speed_type[0];
                $type = $speed_type[1];
                $query->where('packages.speed', '=', $speed);
                $query->where('packages.type', '=', $type);
            })
            ->when($request->township, function ($query, $township) use ($all_township) {
                if ($township == 'empty') {
                    $query->whereNotIn('customers.township_id', $all_township);
                } else {
                    $query->where('customers.township_id', '=', $township);
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $startDate = Carbon::parse($order[0])->format('Y-m-d');
                $endDate = Carbon::parse($order[1])->format('Y-m-d');
                $query->whereBetween('customers.order_date',  [$startDate, $endDate]);
            })
            ->when($request->prefer, function ($query, $prefer) {
                $startDate = Carbon::parse($prefer[0])->format('Y-m-d');
                $endDate = Carbon::parse($prefer[1])->format('Y-m-d');
                $query->whereBetween('customers.prefer_install_date', [$startDate, $endDate]);
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->sh_vlan, function ($query, $vlan) {
                $query->where('customers.vlan', $vlan);
            })

            ->when($request->sh_onu_serial, function ($query, $sh_onu_serial) {
                $query->where('customers.onu_serial', $sh_onu_serial);
            })
            ->when($request->sh_installation_team, function ($query, $sh_installation_team) {
                $query->where('customers.subcom_id', $sh_installation_team['id']);
            })
            ->when($request->sh_sale_person, function ($query, $sh_sale_person) {
                $query->where('customers.sale_person_id', $sh_sale_person['id']);
            })
            ->when($request->sort, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort == 'cid') {
                    $sort_by = 'customers.id';
                } elseif ($sort == 'cname') {
                    $sort_by = 'customers.name';
                } elseif ($sort == 'township') {
                    $sort_by = 'townships.name';
                } elseif ($sort == 'package') {
                    $sort_by = 'packages.name';
                } elseif ($sort == 'order') {
                    $sort_by = 'customers.order_date';
                }

                $query->orderBy($sort_by, 'desc');
            }, function ($query) {
                $query->orderBy('customers.id', 'desc');
            })
            ->select('customers.*', 'projects.name as project_name', 'pops.site_name', 'pop_devices.device_name', 'sn_ports.name as sn_port', 'dn_ports.name as dn_port', 'dn_ports.gpon_frame', 'dn_ports.gpon_slot', 'dn_ports.gpon_port');
        return $mycustomer;
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'NRC',
            'Phone No.',
            'Social Account',
            'Email',

            'Project',
            'Address',
            'Lat Long',
            'Township',
            'Package',
            'Bandwidth',
            'Extra Bandwidth',

            'Installation Team',
            'Sale Person',
            'Sale Source',
            'Sale Remark',
            'Order Date',
            'Prefer Install Date',
            'Installation Date',
            'Installation Remark',
            'Service Activation Date',
            'Fiber Distance',
            'ONU Serial',
            'ONU Power',
            'POP Site',
            'GPON OLT',
            'GPON Frame',
            'GPON Slot',
            'GPON Port',
            'ONT ID',
            'Port Balance',
            'DN',
            'SN',
            'VLAN',
            'WLAN Name',
            'WLAN Password',
            'PPPOE Account',
            'PPPOE Password',
            'Devices',
            'Status',
            'Customer Type',

        ];
    }
    public function columnFormats(): array
    {
        return [
            'R' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'S' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'T' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'V' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'AB' => StyleNumberFormat::FORMAT_TEXT,
        ];
    }
    public function map($mycustomer): array
    {
        $township = Township::find($mycustomer->township_id);
        $package = Package::find($mycustomer->package_id);
        $subcom = User::find($mycustomer->subcom_id);
        $status = Status::find($mycustomer->status_id);
        $sale_person = User::find($mycustomer->sale_person_id);
        $bundle = '';
        if (!empty($mycustomer->bundle)) {
            // Split bundle IDs into an array, handling both single and comma-separated values
            $bundle_ids = array_map('trim', explode(',', $mycustomer->bundle));

            // Initialize an array to store bundle names
            $bundle_names = [];

            // Loop through each bundle ID, fetch the name, and add it to the names array if found
            foreach ($bundle_ids as $bundle_id) {
                $bundle_device = BundleEquiptment::find($bundle_id);
                if ($bundle_device) {
                    $bundle_names[] = $bundle_device->name;
                }
            }

            // Join all found bundle names with a comma separator
            $bundle = implode(', ', $bundle_names);
        }
        return [
            $mycustomer->ftth_id,
            $mycustomer->name,
            $mycustomer->nrc,
            ($mycustomer->phone_1) ? $mycustomer->phone_1 : null,
            ($mycustomer->social_account) ? $mycustomer->social_account : null,
            ($mycustomer->email) ? $mycustomer->email : null,
            $mycustomer->project_name,
            $mycustomer->address,
            $mycustomer->location,
            (isset($township->name)) ? $township->name : '',
            (isset($package->name)) ? $package->name : '',
            (isset($package->speed)) ? $package->speed . ' Mbps' : '',
            ($mycustomer->extra_bandwidth) ? $mycustomer->extra_bandwidth . ' Mbps' : '',

            (isset($subcom->name)) ? $subcom->name : '',
            (isset($sale_person->name)) ? $sale_person->name : '',
            $mycustomer->sale_channel,
            $mycustomer->sale_remark,
            ($mycustomer->order_date) ? Date::stringToExcel($mycustomer->order_date) : null,
            ($mycustomer->prefer_install_date) ? Date::stringToExcel($mycustomer->prefer_install_date) : null,
            ($mycustomer->installation_date) ? Date::stringToExcel($mycustomer->installation_date) : null,
            $mycustomer->installation_remark,
            ($mycustomer->service_activation_date) ? Date::stringToExcel($mycustomer->service_activation_date) : null,

            $mycustomer->fiber_distance,
            $mycustomer->onu_serial,
            $mycustomer->onu_power,
            $mycustomer->site_name,
            $mycustomer->device_name,
            $mycustomer->gpon_frame,
            $mycustomer->gpon_slot,
            $mycustomer->gpon_port,
            $mycustomer->gpon_ontid,
            $mycustomer->port_balance,
            $mycustomer->dn_port,
            $mycustomer->sn_port,

            $mycustomer->vlan,
            $mycustomer->wlan_ssid,
            $mycustomer->wlan_password,

            $mycustomer->pppoe_account,
            $mycustomer->pppoe_password,
            $bundle,
            $status->name,
            ($mycustomer->customer_type) ? $this->getCustomerType($mycustomer->customer_type) : "Normal Customer",
        ];
    }

    public function getCustomerType($type)
    {
        switch ($type) {
            case 1:
                return "Normal Customer";
                break;
            case 2:
                return "VIP Customer";
                break;
            case 3:
                return "Partner Customer";
                break;
            case 4:
                return "Office Staff";
                break;
            default:
                return "Normal Customer";
                break;
        }
    }
}
