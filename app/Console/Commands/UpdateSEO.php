<?php

namespace App\Console\Commands;

use App\Models\Office;
use App\Models\Seo;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateSEO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:fix';

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
        $this->info('Started...');
        $this->reset_pwd();
        $this->info('Done...');
    }

    private function fix()
    {
        $this->info('Started...');
        $all = Office::all();

        $delete_array = array('presses','trainings');

        DB::statement("delete from pndc_seo_detail where seo_id in (select id from pndc_seo where page_name in ('presses','trainings'));");
        DB::statement("delete from pndc_seo where page_name in ('presses','trainings');");

        $arr = ['guidelines','login credentials','office chiefs','staffs','vehicles','circulars'];

        foreach ($arr as $item){
            $exists = Seo::where(['office_id'=>4, 'page_name'=>$item])->exists();
            if(!$exists){
                Seo::create([
                    'office_id'=>4,
                    'page_name'=>$item,
                    'is_active'=>'1',
                    'is_deleted'=> '0',
                    'created_by' => 2,
                    'updated_by' => 2,
                ]);
            }
        }



        $this->info('Done...');
    }

    private function reset_pwd()
    {
        $user = User::where('email_address','hmisict@dor.gov.np')->first();
        $user->password = Hash::make('12345678');
        $user->save();
    }
}
