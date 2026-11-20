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

class DORNewUpdate2021 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:pages';

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
        //loop over each office
        $all = Office::all();
        $home_urls = ['facebook-feed', 'instagram-feed'];

        // DB::statement("delete from pndc_home where home_url in ('information-officer','privacy-policy','terms-and-conditions','facebook-feed','instagram-feed');");
        // if few rows like home_url=>facebook-feed, instagram-feed, information-officer not inserted yet, insert them
        foreach ($all as $item) {
            foreach ($home_urls as $key => $url) {
                $home = Home::where(['office_id' => $item->id, 'home_url' => $url])->first();
                if (!$home) {
                    Home::create([
                        'office_id' => $item->id,
                        'home_url' => $url,
                        'show_in' => $item->id,
                        'created_by' => 2,
                        'updated_by' => 2,
                        'is_active' => '1',
                        'is_deleted' => '0',
                        'home_attachment' => ''
                    ]);
                }
            }
        }

        $languages = Language::getActiveLanguages();

        //for main office, add few more items
        $main_home_urls = ['information-officer', 'privacy-policy', 'terms-and-conditions'];
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
                    'home_attachment' => ''
                ]);
            }

            //insert home detail also for the same
            $insertArray = [];
            // DB::statement('delete from pndc_home_detail where home_id='.$home->id.';');
            foreach ($languages as $language) {
                $head = '';
                if($url=='information-officer') $head = 'Information Officer';
                else if($url=='privacy-policy') $head = 'Privacy Policy';
                else if($url=='terms-and-conditions') $head = 'Terms and Conditions';

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

        //maintain data_x_title and data_x for various rows
        $homes = Home::where('office_id', 4)->whereIn('home_url', ['message-from-director-general', 'information-officer', 'dor-spoke-person'])->get();
        if (count($homes)) {
            foreach ($homes as $key => $home) {
                $home->data_1_title = 'Name in Nepali';
                $home->data_2_title = 'Name in English';
                $home->data_3_title = 'Phone Number';
                $home->data_4_title = 'Email Address';
                $home->save();
            }
        }

        $this->info('DB updated applied successfully.');
    }
}
