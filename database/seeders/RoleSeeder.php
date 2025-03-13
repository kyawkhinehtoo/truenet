<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Role::truncate();
        Role::create(
            [
                'name' => 'Super Administrator',
                'permission' => 'address,advance_payment,advance_payment_day,bundle,contract_term,customer_type,deleted,extra_bandwidth,fc_damaged,fc_used,fiber_distance,foc,foc_period,ftth_id,id,installation_date,installation_remark,location,name,nrc,onu_power,onu_serial,order_date,package_id,phone_1,phone_2,pppoe_account,pppoe_password,prefer_install_date,project_id,sale_channel,sale_person_id,sale_remark,sn_id,splitter_no,status_id,subcom_id,township_id',
                'delete_customer' => 1,
                'read_customer' => 0,
                'read_incident' => 1,
                'write_incident' => 1,
                'edit_invoice' => 1,
                'bill_generation' => 1,
                'bill_receipt' => 1,
                'radius_read' => 1,
                'radius_write' => 1,
                'incident_report' => 1,
                'bill_report' => 1,
                'radius_report' => 1,
                'incident_only' => 0,
                'read_only_ip' => null,
                'add_ip' => null,
                'edit_ip' => 0,
                'delete_ip' => 1,
                'ip_report' => 1,
                'delete_invoice' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Role::create([
                'name' => 'Cashier',
                'permission' => 'address,advance_payment,advance_payment_day,contract_term,customer_type,extra_bandwidth,foc,foc_period,ftth_id,location,name,nrc,order_date,package_id,phone_1,phone_2,prefer_install_date,sale_channel,sale_person_id,sale_remark,status_id,township_id,project_id',
                'delete_customer' => 0,
                'read_customer' => 0,
                'read_incident' => 1,
                'write_incident' => 0,
                'edit_invoice' => 0,
                'bill_generation' => 0,
                'bill_receipt' => 0,
                'radius_read' => 0,
                'radius_write' => 0,
                'incident_report' => 0,
                'bill_report' => 0,
                'radius_report' => 0,
                'incident_only' => null,
                'read_only_ip' => null,
                'add_ip' => null,
                'edit_ip' => 0,
                'delete_ip' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        
    }
}
