<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CustomerController;

class ImportRadiusCommand extends Command
{
    protected $signature = 'radius:import';
    protected $description = 'Import radius users from radius server';

    public function handle()
    {
        $controller = new CustomerController();
        $controller->importRadius();
        
        return Command::SUCCESS;
    }
}
