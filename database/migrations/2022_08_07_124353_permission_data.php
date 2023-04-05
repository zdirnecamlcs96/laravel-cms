<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Local\CMS\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (config('modules.permissions') as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::updateOrCreate([
                    "name" => "{$module}_{$permission}",
                    "guard_name" => "admin"
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
