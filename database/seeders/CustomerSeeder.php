<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $faker = Faker::create();
        foreach (range(1, 50) as $index) {
            $latitude = $faker->latitude(16.7, 17.2); // Adjust the range according to Yangon's latitude
            $longitude = $faker->longitude(96.0, 96.3); // Adjust the range according to Yangon's longitude
            $phoneNumberFormat = '09#########';
            Customer::create([
                'ftth_id' => $faker->unique()->numerify('FTTH-#####'),
                'name' => $faker->name,
                'nrc' => $faker->numerify('##/L(N)###(###)'),
                'phone_1' => $faker->unique()->numerify($phoneNumberFormat),
                'phone_2' => $faker->unique()->numerify($phoneNumberFormat),
                'address' => $faker->address,
                'location' => "$latitude, $longitude",
                'order_date' => $faker->dateTimeBetween('-1 year', 'now'),
                'installation_date' => $faker->dateTimeBetween('now', '+1 month'),
                'prefer_install_date' => $faker->dateTimeBetween('now', '+1 month'),
                'sale_channel' => $faker->randomElement(['Online', 'In-store', 'Agent']),
                'sale_remark' => $faker->sentence,
                'township_id' => $faker->numberBetween(1, 33), // Assuming 33 townships as per previous conversation
                'package_id' => 1, // Adjust according to your package IDs
                'sale_person_id' => 2, // Adjust according to your sale person IDs
                'status_id' => $faker->numberBetween(1, 5), // Adjust according to your status IDs
                'subcom_id' => 1, // Adjust according to your subcom IDs
                'sn_id' => 1,
                'splitter_no' => $faker->numberBetween(1, 5),
                'fiber_distance' => $faker->numberBetween(100, 400),
                'installation_remark' => $faker->sentence,
                'fc_used' => $faker->boolean,
                'fc_damaged' => $faker->boolean,
                'onu_serial' => $faker->unique()->numerify('ONU-#####'),
                'onu_power' => $faker->randomFloat(2, 10, 50),
                'contract_term' => $faker->numberBetween(6, 12),
                'foc' => null,
                'foc_period' => null,
                'advance_payment' => 0,
                'advance_payment_day' => null,
                'extra_bandwidth' => null,
                'deleted' => 0,
                'pop_device_id'=>1,
                'pppoe_account' => $faker->userName,
                'pppoe_password' => $faker->password,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}