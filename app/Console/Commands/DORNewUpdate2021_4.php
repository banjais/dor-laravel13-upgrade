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

class DORNewUpdate2021_4 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:footer';

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

        //loop over each office
        $all = Office::all();
        $home_urls = ['footer-section-1', 'footer-section-2', 'footer-section-3'];

        // DB::statement("delete from pndc_home where home_url in ('information-officer','privacy-policy','terms-and-conditions','facebook-feed','twitter-feed');");
        //if few rows like home_url=>facebook-feed, twitter-feed, information-officer not inserted yet, insert them
        foreach ($home_urls as $key => $url) {
            $home = Home::where(['office_id' => 4, 'home_url' => $url])->first();
            if (!$home) {
                Home::create([
                    'office_id' => 4,
                    'home_url' => $url,
                    'show_in' => 0,
                    'created_by' => 2,
                    'updated_by' => 2,
                    'is_active' => '1',
                    'is_deleted' => '0',
                    'home_attachment' => '',
                    'home_identity_name' =>'FOOTER'
                ]);
            }
        }


        $this->info('DB updated applied successfully.');
    }
}
