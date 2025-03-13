<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Township;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TownshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        City::truncate();
        Township::truncate();
        City::create(
            [
                'name' => 'Yangon',
                'short_code' => 'YGN',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        Township::create(
            [
                'name' => 'Ahlon',
                'short_code' => 'AHN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Bahan',
                'short_code' => 'BHN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Botataung',
                'short_code' => 'BTT',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Dagon Seikkan',
                'short_code' => 'DSK',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Dagon',
                'short_code' => 'DGN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Dala',
                'short_code' => 'DLA',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Dawbon',
                'short_code' => 'DBN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'East Dagon',
                'short_code' => 'EDG',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Hlaing',
                'short_code' => 'HLG',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Hlaingthaya',
                'short_code' => 'HLY',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Insein',
                'short_code' => 'ISN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Kamayut',
                'short_code' => 'KMT',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Kyauktada',
                'short_code' => 'KTA',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Kyimyindaing',
                'short_code' => 'KMD',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Lanmadaw',
                'short_code' => 'LMD',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Latha',
                'short_code' => 'LTH',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Mayangon',
                'short_code' => 'MYG',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Mingala Taungnyunt',
                'short_code' => 'MTT',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Mingaladon',
                'short_code' => 'MGD',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'North Dagon',
                'short_code' => 'NDG',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'North Okkalapa',
                'short_code' => 'NOK',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Pabedan',
                'short_code' => 'PBD',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Pazundaung',
                'short_code' => 'PZD',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Sanchaung',
                'short_code' => 'SCN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Seikkan',
                'short_code' => 'SKN',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Seikkyi Kanaungto',
                'short_code' => 'SKK',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Shwepyitha',
                'short_code' => 'SWP',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'South Dagon',
                'short_code' => 'SDG',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'South Okkalapa',
                'short_code' => 'SOK',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Tamwe',
                'short_code' => 'TMW',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Thaketa',
                'short_code' => 'TKT',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Thingangyun',
                'short_code' => 'TGY',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
TOWNSHIP::CREATE( [
                'name' => 'Yankin',
                'short_code' => 'YNK',
                'city_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
