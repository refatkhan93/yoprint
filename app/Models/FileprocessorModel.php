<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\LazyCollection;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DataProcessingModel;

class FileprocessorModel extends Model
{
    use HasFactory;
    protected $table = 'fileuploadrecord';
    protected $fillable = ['filename', 'filelocation', 'status'];

    public const PENDING = 1;
    public const PROCESSING = 2;
    public const COMPLETED = 3;
    public const FAILED = 4;
    public function getStatus($status)
    {
        switch($status){
            case self::PENDING:
                return 'Pending';
            case self::PROCESSING:
                return 'Processing';
            case self::COMPLETED:
                return 'Completed';
            case self::FAILED:
                return 'Failed';
        }
    }
    public function processFile() // insert this function inside job
    {
        $this->status = self::PROCESSING;
        $this->save();

        $dataProcessingModel = new DataProcessingModel();
        $priority = ['UNIQUE_KEY','PRODUCT_TITLE','PRODUCT_DESCRIPTION','STYLE#','SANMAR_MAINFRAME_COLOR','SIZE','COLOR_NAME','PIECE_PRICE'];
        $file_path = storage_path('app/public/'.$this->filelocation);
        $csv = Reader::createFromPath($file_path, 'r');

        $csv->setHeaderOffset(0);
        $data = $csv->getRecords();
        $clear_data_merged = [];
        foreach ($data as $key => $value) {
            $clear_data = [];
            $clear_data['csv_file_id'] = $this->id;
            foreach ($value as $k => $v) {
                if(in_array($k, $priority))
                {
                    $clear_data[$k] = iconv("UTF-8","UTF-8//IGNORE",$v); // mb_convert_encoding($v, 'UTF-8', mb_detect_encoding($v));
                }
            }
            $clear_data_merged[] = $clear_data;
        }
        $status = $dataProcessingModel->insert_after_check($clear_data_merged);

        $this->status = $status ? self::COMPLETED : self::FAILED;
        $this->save();
    }
}
