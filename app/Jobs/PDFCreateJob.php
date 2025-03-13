<?php

namespace App\Jobs;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\PdfGenerationProgress;
use App\Http\Controllers\Controller;
use App\Traits\PdfTrait;

class PDFCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, PdfTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $invoice_id;
    public function __construct($id)
    {
        $this->invoice_id = $id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $this->invoice_id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();
        $options = [
            'default_font_size' => '11',
            'orientation'   => 'P',
            'encoding'      => 'UTF-8',
            'margin_top'  => 45,
            'title' => $invoice->ftth_id,
        ];
        $name = date("ymdHis") . '-' . $invoice->bill_number . ".pdf";
        $path = $invoice->ftth_id . '/' . $name;
        $pdf = $this->createPDF($options, 'invoice', $invoice, $name, $path);

        if ($pdf['status'] == 'success') {

            // Successfully stored. Return the full path.
            $invoice->invoice_file =  $pdf['disk_path'];
            $invoice->invoice_url = $pdf['shortURL'];
            $invoice->update();
            $app_url = getenv('APP_URL', 'http://localhost:8000');

            $all_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                ->count();
            $file_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
                ->whereNotNull('invoice_file')
                ->count();
            $progress = round(($file_invoice / $all_invoice) * 100);
            event(new PdfGenerationProgress($progress, $this->invoice_id, $pdf['disk_path'], $pdf['shortURL']));
        }

        //Testing Block
        // $invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
        //     ->join('packages', 'customers.package_id', 'packages.id')
        //     ->where('invoices.id', '=', $this->invoice_id)
        //     ->select('invoices.*', 'packages.type as service_type')
        //     ->first();
        // if ($invoice) {
        //     $invoice->invoice_file = "Test";
        //     $invoice->invoice_url = "Test";
        //     if ($invoice->update()) {

        //         $all_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
        //             ->count();
        //         $file_invoice = Invoice::where('bill_id', '=', $invoice->bill_id)
        //             ->whereNotNull('invoice_file')
        //             ->count();
        //         $progress = round(($file_invoice / $all_invoice) * 100);

        //         //PdfGenerationProgress::dispatchSync($progress);

        //         //event(new PdfGenerationProgress($progress));
        //         event(new PdfGenerationProgress($progress));
        //         sleep(1);
        //     }
        // }
    }
}
