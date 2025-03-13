<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;

class FileController extends Controller
{
    public function upload(Request $request){
        
        $request->validate([
            'name' => 'required',
           'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:2048'
        ]);

        $fileUpload = new FileUpload;

        if($request->file()) {
            
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            if($request->incident_id){
                $fileUpload->incident_id = $request->incident_id;
            }
            if($request->customer_id){
                $fileUpload->customer_id = $request->customer_id;
            }
            $fileUpload->name = $request->name;
            $fileUpload->path = '/storage/' . $file_path;
            $fileUpload->save();

            return redirect()->back()->with('message', 'File Uploaded Successfully.');
        }
   }
}
