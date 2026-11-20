<?php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Models\Publicationdetail;
use Illuminate\Console\Command;

class HousekeepingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'house:keep';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $publications = Publication::where('office_id',147)->get();
        foreach ($publications as $item) {
            $file_path = public_path('uploads/publication/'.$item->publication_attachment) ;
            if(file_exists($file_path)){
                unlink($file_path);
                $details = Publicationdetail::where('publication_id', $item->id)->delete();
                $item->delete();
            }
        }
    }
}
