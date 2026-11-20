<?php

namespace App\Console\Commands;

use App\Models\Home;
use App\Models\Homedetail;
use App\Models\Language;
use App\Models\Office;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DORNewUpdate2021_3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:screen_reader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply new db changes for new works in 2021';

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
        $this->insertHomes();
    }

    private function insertHomes()
    {
        $languages = Language::getActiveLanguages();

        //for main office, add few more items
        $main_home_urls = ['screen-reader'];
        foreach ($main_home_urls as $key => $url) {
            $home = Home::where(['office_id' => 4, 'home_url' => $url])->first();
            if (!$home) {
                $home = Home::create([
                    'office_id' => 4,
                    'home_url' => $url,
                    'show_in' => 4,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'is_active' => '1',
                    'is_deleted' => '0',
                    'home_attachment' => '',
                    'home_identity_name' => 'SCREEN READER'
                ]);
            }
            else{
                $home->home_identity_name = 'SCREEN READER';
                $home->save();
            }

            //insert home detail also for the same
            $insertArray = [];
            // DB::statement('delete from pndc_home_detail where home_id='.$home->id.';');
            foreach ($languages as $language) {
                $head = 'Screen Reader';

                $insertArray[] = [
                    'home_id'                => $home->id,
                    'language_id'            => $language->id,
                    'home_heading'           => $head,
                    'home_description'       => '--',
                    'home_page_title'        => '--',
                    'created_at'             => date("Y-m-d H:i:s"),
                    'updated_at'             => date("Y-m-d H:i:s"),
                ];
            }

            Homedetail::insert($insertArray);
        }

        $this->info('DB updated applied successfully.');
    }
}
