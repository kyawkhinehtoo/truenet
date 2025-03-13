<?php

namespace App\Imports;

use App\Models\BundleEquiptment;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;
use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\DnPorts;
use App\Models\SnPorts;
use App\Models\Subcom;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
  /**
   * @param array $row
   *
   * @return \Illuminate\Database\Eloquent\Model|null
   */
  public function model(array $row)
  {

    $township_id = (trim($row['township'])  != "") ? Township::where('name', trim($row['township']))->first() : null;
    $package_id = (trim($row['package'])  != "") ? Package::where('name', trim($row['package']))->first() : null;
    $project_id = (trim($row['project']) != "") ? Project::where('name', trim($row['project']))->first() : null;
    $sale_person_id = (trim($row['sale_person'])  != "") ? User::where('name', trim($row['sale_person']))->first() : null;
    $status_id = (trim($row['status'])  != "") ? Status::where('name', trim($row['status']))->first() : null;
    $subcom_id = (trim($row['installation_team'])  != "") ? Subcom::where('name', trim($row['installation_team']))->first() : null;
    $sn_id = (trim($row['sn'])  != "") ? SnPorts::where('sn_ports.name', trim($row['sn']))->first() : null;
    $pop_id = (trim($row['sn'])  != "") ? 1 : null;
    $pop_device_id = (trim($row['sn'])  != "") ? 1 : null;
    $bundle = (trim($row['devices'])  != "") ? BundleEquiptment::where('name', trim($row['devices']))->first() : null;



    $cus = Customer::where('ftth_id', '=', trim($row['id']))->first();
    if ($cus) {
      $cus->ftth_id = trim($row['id']);
      $cus->name = trim($row['name']);
      $cus->phone_1 = trim($row['phone_no']);
      $cus->address = trim($row['address']);
      $cus->location = trim($row['lat_long']);
      $cus->order_date = (trim($row['order_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['order_date'])) : null;
      $cus->installation_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $cus->prefer_install_date = (trim($row['prefer_install_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['prefer_install_date'])) : null;
      $cus->service_activation_date = (trim($row['service_activation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_activation_date'])) : null;
      $cus->sale_channel = trim($row['sale_source']);
      $cus->sale_remark = trim($row['sale_remark']);
      $cus->township_id = ($township_id) ? $township_id->id : null;
      $cus->package_id = ($package_id) ? $package_id->id : null;
      $cus->sale_person_id = ($sale_person_id) ? $sale_person_id->id : null;
      $cus->status_id = ($status_id) ? $status_id->id : 1;
      $cus->subcom_id = ($subcom_id) ? $subcom_id->id : null;
      $cus->sn_id = ($sn_id) ? $sn_id->id : null;
      $cus->pop_id = $pop_id;
      $cus->onu_serial = trim($row['onu_serial']);
      $cus->deleted = 0;
      $cus->pppoe_account = trim($row['pppoe_account']);
      $cus->pppoe_password = trim($row['pppoe_password']);
      $cus->bundle = ($bundle) ? $bundle->id : null;
      $cus->vlan = trim($row['vlan']);
      $cus->wlan_ssid = trim($row['wlan_name']);
      $cus->wlan_password = trim($row['wlan_password']);
      $cus->pop_device_id = $pop_device_id;
      $cus->gpon_ontid = trim($row['ont_id']);

      $cus->nrc = trim($row['nrc']);
      $cus->social_account = trim($row['social_account']);
      $cus->email = trim($row['email']);
      $cus->project_id = ($project_id) ? $project_id->id : null;
      $cus->extra_bandwidth = trim($row['extra_bandwidth']);
      $cus->installation_remark = trim($row['installation_remark']);
      $cus->fiber_distance = trim($row['fiber_distance']);
      $cus->onu_power = trim($row['onu_power']);
      $cus->port_balance = trim($row['port_balance']);
      $cus->customer_type =  $this->getCustomerTypeId($row['customer_type']);
      $cus->service_off_date = (trim($row['service_end_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_end_date'])) : null;


      $cus->update();
      Storage::append('CustomerImport.log', trim($row['id']) . ' Update !');
    } else {
      $customer = new Customer();
      $customer->ftth_id = trim($row['id']);
      $customer->name = trim($row['name']);
      $customer->phone_1 = trim($row['phone_no']);
      $customer->address = trim($row['address']);
      $customer->location = trim($row['lat_long']);
      $customer->order_date = (trim($row['order_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['order_date'])) : null;
      $customer->installation_date = (trim($row['installation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['installation_date'])) : null;
      $customer->prefer_install_date = (trim($row['prefer_install_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['prefer_install_date'])) : null;
      $customer->service_activation_date = (trim($row['service_activation_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_activation_date'])) : null;
      $customer->sale_channel = trim($row['sale_source']);
      $customer->sale_remark = trim($row['sale_remark']);
      $customer->township_id = ($township_id) ? $township_id->id : null;
      $customer->package_id = ($package_id) ? $package_id->id : null;
      $customer->sale_person_id = ($sale_person_id) ? $sale_person_id->id : null;
      $customer->status_id = ($status_id) ? $status_id->id : 1;
      $customer->subcom_id = ($subcom_id) ? $subcom_id->id : null;
      $customer->sn_id = ($sn_id) ? $sn_id->id : null;
      $customer->pop_id = $pop_id;
      $customer->onu_serial = trim($row['onu_serial']);
      $customer->deleted = 0;
      $customer->pppoe_account = trim($row['pppoe_account']);
      $customer->pppoe_password = trim($row['pppoe_password']);
      $customer->customer_type = 1;
      $customer->bundle = ($bundle) ? $bundle->id : null;
      $customer->project_id = $project_id;
      $customer->vlan = trim($row['vlan']);
      $customer->wlan_ssid = trim($row['wlan_name']);
      $customer->wlan_password = trim($row['wlan_password']);
      $customer->pop_device_id = $pop_device_id;
      $customer->gpon_ontid = trim($row['ont_id']);
      $customer->nrc = $row['nrc'];
      $customer->social_account = $row['social_account'];
      $customer->email = $row['email'];
      $customer->project_id = ($project_id) ? $project_id->id : null;
      $customer->extra_bandwidth = $row['extra_bandwidth'];
      $customer->installation_remark = $row['installation_remark'];
      $customer->fiber_distance = $row['fiber_distance'];
      $customer->onu_power = $row['onu_power'];
      $customer->gpon_ontid = $row['ont_id'];
      $customer->port_balance = $row['port_balance'];
      $customer->customer_type =  $this->getCustomerTypeId($row['customer_type']);
      $customer->service_off_date = (trim($row['service_end_date'])) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject(trim($row['service_end_date'])) : null;
      $customer->save();
      Storage::append('CustomerImport.log', trim($row['id']) . ' Save!');
      return $customer;
    }
  }
  function getCustomerTypeId($customerType)
  {
    $customerTypes = [
      "Normal Customer" => 1,
      "VIP Customer" => 2,
      "Partner Customer" => 3,
      "Office Staff" => 4,
    ];

    return $customerTypes[trim($customerType)] ?? null;
  }
}
