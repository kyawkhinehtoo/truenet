<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CustomerController;

class ExpireToSuspendCommand extends Command
{
    protected $signature = 'customers:expire-to-suspend';
    protected $description = 'Change status of expired customers to suspended';

    public function handle()
    {
        $controller = new CustomerController();
        $controller->expireToSuspend();
        
        $this->info('Expired customers have been suspended successfully.');
        
        return Command::SUCCESS;
    }
}