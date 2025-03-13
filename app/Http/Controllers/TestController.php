<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;

use App\Models\Customer;

use ArrayIterator;
use CachingIterator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Storage;
use DateTime;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(){
        $status = Status::when($request->status, function($query, $pkg){
            $query->where('name','LIKE','%'.$pkg.'%');
        })
        ->paginate(10);
       return Inertia::render('Test', ['status' => $status]);
    }
    public function makeSinglePDF(Request $request)
    {
        $invoice = Invoice::find($request->id);

        $options = [
            'enable-local-file-access' => true,
            'orientation'   => 'portrait',
            'encoding'      => 'UTF-8',
            'footer-spacing' => 5,
            'header-spacing' => 5,
            'margin-top'  => 20,
            'footer-html'   => view('footer')
        ];

        view()->share('voucher', $invoice);
        $pdf = PDF::loadView('voucher', $invoice);

        $pdf->setOptions($options);
        $output = $pdf->output();
        $name = $invoice->bill_number . ".pdf";
        $disk = Storage::disk('public');

        if ($disk->put($invoice->ftth_id . '/' . $name, $output)) {
            // Successfully stored. Return the full path.
            $invoice->file =  $disk->path($invoice->ftth_id . '/' . $name);
            // $builder = new \AshAllenDesign\ShortURL\Classes\Builder();

            // $shortURLObject = $builder->destinationUrl('https://oss.marga.com.mm/storage/'.$invoice->ftth_id.'/'.$name)->make();
            // $shortURL = $shortURLObject->url_key;
            // $invoice->url = $shortURL;

            $invoice->update();
        }

        // download PDF file with download method
        return redirect()->back()->with('message', 'PDF Generated Successfully.');
    }

  
    public function sanitisePhone($phone_no){
        $phone_no = trim($phone_no);
        $pattern_9 = "/^(9)[0-9]{7,9}$/";
        $pattern_09 = "/^(09)[0-9]{7,9}$/";
        $pattern_959 = "/^(959)[0-9]{7,9}$/";
        $pattern_plus959 = "/^(\+959)[0-9]{7,9}$/";
        $pattern_zero959 = "/^(0959)[0-9]{7,9}$/";
        $pattern_overnum = "/^(099)[0-9]{9,12}$/";
        $new_phone = $phone_no;
        switch($phone_no){
           
            case preg_match($pattern_9, $phone_no)==true:
               // echo 'this is 9 pattern';
                
                if(substr($phone_no,0,1)=='9')
                {
                    $new_phone= '0'.$phone_no;
                }
            break;
            case preg_match($pattern_959, $phone_no)==true:
              //  echo 'this is 959 pattern';
                
                if(substr($phone_no,0,3)=='959')
                {
                    $new_phone= '09'.substr($phone_no,3);
                }
            break;
            case preg_match($pattern_plus959, $phone_no)==true:
               // echo 'this is +959 pattern';
                 if(substr($phone_no,0,4)=='+959')
                {
                    $new_phone= '09'.substr($phone_no,4);
                }
             case preg_match($pattern_zero959, $phone_no)==true:
               // echo 'this is 0959 pattern';
                 if(substr($phone_no,0,4)=='0959')
                {
                    $new_phone= '09'.substr($phone_no,4);
                }
            break;
            case preg_match($pattern_09, $phone_no)==true:
                $new_phone= $phone_no;
            break;
            case preg_match($pattern_overnum, $phone_no)==true:
               // echo 'this is overnum pattern';
                 if(substr($phone_no,0,3)=='099')
                {
                    $new_phone= '09'.substr($phone_no,3);
                }
            break;
        }
        return $new_phone;
    }
    
    public function sanitiseAllPhone(){
        $customers = Customer::all();
        foreach ($customers as  $customer) {
            $new_cus = Customer::find($customer->id);
            $new_cus->phone_1 = ($customer->phone_1)?$this->checkPhoneArray($customer->phone_1):null;
            $new_cus->phone_2 = ($customer->phone_2)?$this->checkPhoneArray($customer->phone_2):null;
            $new_cus->save();
        }
    }
    public function checkPhoneArray($cus_phone){
     
        $phones = $cus_phone;
        $data = "";
        $delimiters = array(",",";",":"," ","/");
        $cus_phone = str_replace($delimiters,",",$cus_phone);
         $phones = $cus_phone;
         if (strpos($cus_phone, ',') !== false) {
            $phones = explode(",", $cus_phone);
         }
              
        if (is_array($phones)) {
            $iter = new CachingIterator(new ArrayIterator($phones));
          
            foreach ($iter as $phone) {
                $data .= $this->sanitisePhone($phone);
                if($iter->hasNext()){
                    $data .=",";
                }
            }
        } else {
           $data = $this->sanitisePhone($phones);
        }
        return $data;
    }
    
   

    public function replaceMarkup($data, $id)
    {
        $invoice = Invoice::find($id);

        if ($invoice) {
            $dt = DateTime::createFromFormat('!m', $invoice->bill_month);
            $month = $dt->format('F');
            $bill_url = 'https://oss.marga.com.mm/s/' . $invoice->url;
            $search = array('{{ftth_id}}', '{{bill_number}}', '{{period_covered}}', '{{month}}', '{{year}}', '{{bill_to}}', '{{attn}}', '{{usage_days}}', '{{total_payable}}', '{{payment_duedate}}', '{{url}}');
            $replace = array($invoice->ftth_id, $invoice->bill_number, $invoice->period_covered, $month, $invoice->bill_year, $invoice->bill_to, $invoice->attn, $invoice->usage_days, $invoice->total_payable, $invoice->payment_duedate, $bill_url);
            $replaced = str_replace($search, $replace, $data);
            return $replaced;
        }
    }
    public function destroyPDF(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingTemp::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingTemp::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function destroyall()
    {
        BillingTemp::truncate();
        return redirect()->back();
    }
}
