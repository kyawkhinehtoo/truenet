<?php

namespace App\Traits;

use App\Models\Incident;
use Illuminate\Support\Facades\DB;
use Datetime;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Storage;
use AshAllenDesign\ShortURL\Classes\Builder;
trait PdfTrait
{
    public function createPDF($options, $template, $data, $name, $path)
    {

        view()->share($template, $data);

        $pdf = PDF::loadView($template, $data, [], $options);
        $pdf->getMpdf()->AddPage();
        $pdf->getMpdf()->SetDisplayMode('fullpage', 'two');
        $pdf->getMpdf()->WriteHTML((string)view($template, $data, [], $options));
        $output = $pdf->output();

        $disk = Storage::disk('public');

        if ($disk->put($path, $output)) {
            // Successfully stored. Return the full path.
            $disk_path =  $disk->path($data->ftth_id . '/' . $name);
            $app_url = getenv('APP_URL', 'http://localhost:8000');
            if (!$app_url)
                $app_url = "http://localhost:8000";
                $shortURLObject = app(Builder::class)
            ->destinationUrl($app_url . '/storage/' . $data->ftth_id . '/' . $name)
            ->make();
           
            $shortURL = $shortURLObject->url_key;
            return ['shortURL' => $shortURL, 'disk_path' => $disk_path, 'status' => 'success'];
        }
        return ['status' => 'fail'];
    }
}
