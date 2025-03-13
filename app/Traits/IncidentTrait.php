<?php
namespace App\Traits;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Datetime;
trait IncidentTrait {

    private $min_hr = 4;

    public function getTimeOverdue() {
       // select i.id, i.code,i.date,i.time, c.ftth_id, s.percentage from incidents i join customers c join packages p join sla s where i.customer_id = c.id and c.package_id = p.id and p.sla_id = s.id and i.status <> 0 and i.status <> 2;
       $incident_list = array();
       $incidents = DB::table('incidents')
       ->join('customers', 'incidents.customer_id', '=', 'customers.id')
       ->join('packages','customers.package_id','=','packages.id')
       ->join('sla','packages.sla_id','=','sla.id')
       ->where('incidents.status','<>',3)
       ->where('incidents.status','<>',4)
       ->select(
           'incidents.id',
           'incidents.code',
           'incidents.date',
           'incidents.time',
           'customers.ftth_id as ftth_id',
           'sla.percentage as percentage',)
       ->orderBy('incidents.id','desc')
       ->get();
       if(count($incidents)){
           foreach ($incidents as $incident) {
                $incident_percentage = $this->percentageToSecond($incident->percentage);
                $date_time = $incident->date.' '.$incident->time;
                $diff = $this->dateDiff($date_time);
                if($diff >= $incident_percentage){
                    $incident->diff = $diff;
                    $incident->over = $diff - $incident_percentage;
                    array_push($incident_list,$incident);
                }
           }
       }
       return $incident_list;
    }
    public function getTimeRemain() {
        // select i.id, i.code,i.date,i.time, c.ftth_id, s.percentage from incidents i join customers c join packages p join sla s where i.customer_id = c.id and c.package_id = p.id and p.sla_id = s.id and i.status <> 0 and i.status <> 2;
        $incident_list = array();
        $incidents = DB::table('incidents')
        ->join('customers', 'incidents.customer_id', '=', 'customers.id')
        ->join('packages','customers.package_id','=','packages.id')
        ->join('sla','packages.sla_id','=','sla.id')
        ->where('incidents.status','<>',0)
        ->where('incidents.status','<>',2)
        ->select(
            'incidents.id',
            'incidents.code',
            'incidents.date',
            'incidents.time',
            'customers.ftth_id as ftth_id',
            'sla.percentage as percentage',)
        ->orderBy('incidents.id','desc')
        ->get();
        if(count($incidents)){
            foreach ($incidents as $incident) {
                 $incident_percentage = $this->percentageToSecond($incident->percentage);
                 $date_time = $incident->date.' '.$incident->time;
                 $diff = $this->dateDiff($date_time);
                 if($diff >= ($incident_percentage - $this->toSecond($this->min_hr)) && 
                 $diff < $incident_percentage){
                    $incident->diff = $diff;
                    $incident->over = $diff - $incident_percentage;
                     array_push($incident_list,$incident);
                 }
            }
        }
        return $incident_list;
     }
    public function percentageToSecond($percentage,$period = 'month'){
        $a_week = 604800;
        $a_month = 2592000;
        $a_year = 31536000;
        $seconds = 0;
        if($period == 'week'){
            $seconds = $a_week - ($percentage/100) * $a_week;
        }else if($period == 'year'){
            $seconds = $a_year - ($percentage/100) * $a_year;
        }else{
            $seconds = $a_month - ($percentage/100) * $a_month;
        }
        return $seconds;
    }
    public function toSecond($data,$type='hour'){
        if($type=='day'){
            return $data * 86400;
        }else{
            return $data * 3600;
        }
    }
    public function dateDiff($target){
        $origin = new DateTime('now');
        $target_date = new DateTime($target);
       
        return ($origin->getTimestamp() - $target_date->getTimestamp());
    }
}