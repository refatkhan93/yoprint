<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FileprocessorModel;

class FileprocessorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleare-fileprocessing:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initiating file processing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            FileprocessorModel::where('status', FileprocessorModel::PENDING)->chunk(100, function($files){
                foreach($files as $file){
                    $this->info('File '.$file->id.' is processing');
                    $file->processFile();
                    $this->info('File '.$file->id.' is processed');
                }
            });
        }
        catch(Exception $e){
            $this->error('File processing failed : '. $file->id .' '. $e->getMessage());
        }
    }
}
