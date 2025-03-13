<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\IncidentTrait;
class IncidentAlertController extends Controller
{
    use IncidentTrait;
    public function getOverdue(){
        $over_sla = $this->getTimeOverdue();
        return $over_sla;
    }
    public function getRemain(){
        $close_sla = $this->getTimeRemain();
        return $close_sla;
    }
    
}
