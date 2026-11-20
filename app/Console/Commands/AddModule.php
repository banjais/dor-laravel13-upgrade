<?php

namespace App\Console\Commands;

use App\Models\AccessControlModel;
use App\Models\ModuleModel;
use App\Models\UserTypeModel;
use Illuminate\Console\Command;

class AddModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:add';

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
        $this->addModule('Guidelines');
        $this->addModule('Tools');
        $this->addModule('Office Chiefs');
        $this->info('Completed...');
    }

    private function addModule($module_name)
    {
        $exists = ModuleModel::where('module_name', $module_name)->exists();
        if ($exists) return;

        $data        = array(
            'module_name' => $module_name,
            'is_deleted'                       => '0',
        );
        $module = ModuleModel::create($data);
        if ($module) {
            $usertypes = UserTypeModel::getUndeletedUserTypes();
            foreach ($usertypes as $usertype) {
                if (AccessControlModel::checkAccessListItemByModuleAndUsertype($module->id, $usertype->id)) {
                    if ($usertype->id == 1) {
                        continue;
                    } elseif ($usertype->id == 2) {
                        $data = array(
                            'module_id' => $module->id,
                            'user_type_id'            => $usertype->id,
                            'access_add'              => '1',
                            'access_delete'           => '1',
                            'access_update'           => '1',
                            'access_publish'          => '1',
                            'access_view'             => '1',
                            'access_upload'           => '1',
                            'access_download'         => '1'
                        );
                    } else {
                        $data = array(
                            'module_id' => $module->id,
                            'user_type_id'            => $usertype->id,
                            'access_add'              => '0',
                            'access_delete'           => '0',
                            'access_update'           => '0',
                            'access_publish'          => '0',
                            'access_view'             => '0',
                            'access_upload'           => '0',
                            'access_download'         => '0'
                        );
                    }
                    AccessControlModel::create($data);
                }
            }
            $this->info('Module has been added successfully!!');
        }
    }
}
