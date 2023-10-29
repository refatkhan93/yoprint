<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DataProcessingModel extends Model
{
    use HasFactory;
    protected $table = 'csv_product_details';
    protected $fillable = ['csv_file_id', 'UNIQUE_KEY', 'PRODUCT_TITLE', 'PRODUCT_DESCRIPTION', 'STYLE#', 'SANMAR_MAINFRAME_COLOR', 'SIZE', 'COLOR_NAME', 'PIECE_PRICE'];

    public function insert_after_check($dataset)
    {
        $error_flag = false;
        try{
            DB::beginTransaction();
            foreach ($dataset as $key => $value) {
                $product = DataProcessingModel::where('UNIQUE_KEY', $value['UNIQUE_KEY'])->first();
                if($product){
                    $product->update($value);
                }
                else{
                    self::create($value);
                }
            }
            DB::commit();
            Log::info('ALL Data inserted successfully');
            return !$error_flag;
        }
        catch(Exception $e){
            $error_flag = true;
            Log::error($e->getMessage());
        }
    }
}
