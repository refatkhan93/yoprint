<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileprocessorModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Jobs\FileprocessorJob;

class FileprocessorController extends Controller
{
    public function index()
    {
        return view('upload_pdf');
    }
    public function sendAjxData()
    {
        $files = FileprocessorModel::get()->map(function($file){
            $file->created_at_formated = Carbon::parse($file->created_at)->format('y-m-d h:s a');
            $file->status_formated = FileprocessorModel::getStatus($file->status);
            $file->timesago = Carbon::parse($file->created_at)->diffForHumans();
            $file->location_formated = asset('storage/'.$file->filelocation);
            return $file;
        });
        $data = [];

        foreach ($files as $file) {
            $data[] = [
                'col1' => $file->created_at_formated . '<br> ('.$file->timesago.')',
                'col2' => '<a href="'.$file->location_formated.'">'.$file->filename.'</a>',
                'col3' => $file->status_formated
            ];
        }
        http_response_code(200);
        echo json_encode($data);
        exit();
    }
    public function upload()
    {
        try{
            $file = request()->file('uploaded_file');
            $file_name = $file->getClientOriginalName();
            $file_extension = $file->getClientOriginalExtension();
            $raw_name = explode('.', $file_name);
            $file_name_update =  $raw_name[0].'_'.Carbon::now()->format('YmdHis').'.'.$file_extension;

            if(FileprocessorModel::where('filename', $file_name)->exists()){
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'message' => 'File with same name already exists'));
                exit();
            }

            if($file->isValid() && $file_extension == 'csv' && $file->getSize() > 0 ) {
                $file->storeAs('csv', $file_name_update);
                DB::beginTransaction();
                $fileprocessor = new FileprocessorModel();
                $fileprocessor->filename = $file_name;
                $fileprocessor->filelocation = 'csv/'.$file_name_update;
                $fileprocessor->status = FileprocessorModel::PENDING;
                $fileprocessor->save();
                DB::commit();
                FileprocessorJob::dispatch($fileprocessor);

                http_response_code(200);
                echo json_encode(array('status' => 'success', 'message' => 'File uploaded successfully'));
            }
            else{
                http_response_code(400);
                echo json_encode(array('status' => 'error', 'message' => 'File upload failed'));
            }
            
        }
        catch(Exception $e){
            http_response_code(401);
            echo json_encode(array('status' => 'error', 'message' => 'File upload failed'));
        }
    }
}
