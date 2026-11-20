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

class DORNewUpdate2021_1 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:live';

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
        $this->info('Started...');

        //maintain data_x_title and data_x for various rows
        $home = Home::where('office_id', 4)->where('home_url', 'general-information')->first();

        if ($home) {
            $home->home_identity_name = 'LIVE UPDATE';
            $home->data_1_title = 'Browse Cover Photo';
            $home->data_2_title = 'Choose Icon Color';
            $home->data_3_title = 'Update Name in Nepali';
            $home->data_4_title = 'Update Name in English';
            $home->save();
        }

        //for newly added live items
        $languages = Language::getActiveLanguages();

        $home_urls = ['general-information-2', 'general-information-3'];

        // DB::statement("delete from pndc_home_detail where id in (select id from pndc_home where home_url in ('general-information-2', 'general-information-3'));");
        // DB::statement("delete from pndc_home where home_url in ('general-information-2', 'general-information-3');");

        foreach ($home_urls as $key => $url) {
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
                    'home_identity_name' => 'LIVE UPDATE'
                ]);

                $home->data_1_title = 'Browse Cover Photo';
                $home->data_2_title = 'Choose Icon Color';
                $home->data_3_title = 'Update Name in Nepali';
                $home->data_4_title = 'Update Name in English';
                $home->data_5_title = 'Browse Pdf';
                $home->data_6_title = 'Browse Video';
                $home->save();

                DB::statement('delete from pndc_home_detail where home_id=' . $home->id);

                //insert home detail also for the same
                $insertArray = [];
                foreach ($languages as $language) {
                    $insertArray[] = [
                        'home_id'                => $home->id,
                        'language_id'            => $language->id,
                        'home_heading'           => $home->home_url == 'general-information-2' ? 'Live Update 2' : 'Live Update 3',
                        'home_description'       => '--',
                        'home_page_title'        => '--',
                        'created_at'             => date("Y-m-d H:i:s"),
                        'updated_at'             => date("Y-m-d H:i:s"),
                    ];
                }

                Homedetail::insert($insertArray);
            } else {
                $home->home_identity_name = 'LIVE UPDATE';
                $home->save();
            }
        }


        $this->info('DB updated applied successfully.');
    }
}
