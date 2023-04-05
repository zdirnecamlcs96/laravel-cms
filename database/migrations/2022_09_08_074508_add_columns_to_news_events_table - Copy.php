<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('news_events', function (Blueprint $table) {
            $table->timestamp('display_date')->nullable()->after('categories');
            $table->integer('position')->nullable()->after('display_date');
            $table->integer('status')->default(1)->after('position');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('news_events', function (Blueprint $table) {
            $table->dropColumn('display_date');
            $table->dropColumn('position');
            $table->dropColumn('status');
        });    }
};
