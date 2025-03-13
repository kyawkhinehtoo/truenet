<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;
use DB;

class AutoExpireController extends Controller
{
    public function __construct()
    {
        $radius = RadiusController::checkRadiusEnable();

        if (!$radius) {
            abort(403);
        }
    }
    private function setExpire()
    {
        $expiration_from = '2000-01-01 00:00:00';
        $expiration_to = date('Y-m-d H:i:s');
        $expiration = array('from' => $expiration_from, 'to' => $expiration_to);

        $radius = new RadiusController();
        $radius_users = $radius->getExpiredUser($expiration);
        $radius_users = json_decode($radius_users, true);
        $affectedRows = 0;
        if ($radius_users) {
            foreach ($radius_users as $customer) {
                DB::table('customers')
                    ->join('status', 'status.id', 'customers.status_id')
                    ->where('ftth_id', $customer['username'])
                    ->where('status.type', 'active')
                    ->update(['customers.status_id' => DB::raw('(SELECT id FROM status WHERE name = "Expired")')]);
            }
        }
    }
    private function setActive()
    {

        $radius = new RadiusController();
        $radius_users = $radius->getActiveUser();
        $radius_users = json_decode($radius_users, true);

        if ($radius_users) {
            foreach ($radius_users as $customer) {
                DB::table('customers')
                    ->join('status', 'status.id', 'customers.status_id')
                    ->where('ftth_id', $customer['username'])
                    ->where('status.name', 'Expired')
                    ->update(['customers.status_id' => DB::raw('(SELECT id FROM status WHERE name = "Active")')]);
            }
        }
    }
    public function __invoke()
    {
        $this->setExpire();
        $this->setActive();
    }
}
