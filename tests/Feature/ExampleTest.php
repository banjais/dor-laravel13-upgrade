<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_admin_home(): void
    {
        Schema::create('configuration', function (Blueprint $table) {
            $table->id();
            $table->string('configuration_name')->unique();
            $table->text('configuration_value')->nullable();
        });

        DB::table('configuration')->insert([
            ['configuration_name' => 'absolute_site_path', 'configuration_value' => 'http://localhost'],
            ['configuration_name' => 'relative_site_path', 'configuration_value' => '/'],
            ['configuration_name' => 'site_name', 'configuration_value' => 'DoR Test'],
        ]);

        $this->get('/')->assertRedirect('home');
    }
}
