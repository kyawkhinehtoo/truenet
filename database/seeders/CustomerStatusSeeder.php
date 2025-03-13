<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class CustomerStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        
        Status::create(
            [
                'name' => 'Installation Request',
                'color' => 'green-600',
                'start_date' => 0,
                'end_date' => 0,
                'type' => 'new',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Status::create(
                [
                    'name' => 'Active',
                    'color' => 'blue-900',
                    'start_date' => 0,
                    'end_date' => 0,
                    'type' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Status::create(
                    [
                        'name' => 'Suspended',
                        'color' => 'yellow-700',
                        'start_date' => 1,
                        'end_date' => 1,
                        'type' => 'suspense',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    Status::create(
                        [
                            'name' => 'Terminated',
                            'color' => 'red-600',
                            'start_date' => 1,
                            'end_date' => 0,
                            'type' => 'terminate',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        Status::create(
                            [
                                'name' => 'Pending Installation',
                                'color' => 'pink-600',
                                'start_date' => 0,
                                'end_date' => 0,
                                'type' => 'pending',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            Status::create(
                                [
                                    'name' => 'Port Full',
                                    'color' => 'yellow-600',
                                    'start_date' => 0,
                                    'end_date' => 0,
                                    'type' => 'new',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
      
    }
}
