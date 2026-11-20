<?php

namespace App\Console\Commands;

use App\Models\Home;
use App\Models\Office;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DORNewUpdate2021_2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:pwd';

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
        //update password of super admin
        $users = User::all();
        foreach ($users as $user) {
            $user->password            = Hash::make('DoR@2021??');
            $user->password_changed_on = date('Y-m-d H:i:s');
            $user->save();
        }

        $this->info('DB updated applied successfully.');
    }
}
