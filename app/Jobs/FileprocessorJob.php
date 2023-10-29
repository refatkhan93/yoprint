<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FileprocessorModel;
use Illuminate\Support\Facades\Log;

class FileprocessorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 3;
    public $retryAfter = 3600;
    public $timeout = 3600;
    public $filetoprocessor;

    public function __construct(FileprocessorModel $filetoprocessor)
    {
        $this->filetoprocessor = $filetoprocessor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Log::info('File '. $this->filetoprocessor->id.' is processing');
            $this->filetoprocessor->processFile();
            Log::info('File '. $this->filetoprocessor->id.' is processed');
        }
        catch(Exception $e){
            Log::error('File processing failed : '.  $this->filetoprocessor->id .' '. $e->getMessage());
        }
    }
}
