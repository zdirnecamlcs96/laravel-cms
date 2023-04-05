<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->decimal('size', 30, 8)->comment('in kilobytes');
            $table->string('mime');
            $table->string('extension');
            $table->string('original_name');
            $table->string('low_resolution')->nullable();
            $table->string('high_resolution')->nullable();
            $table->string('path');
            $table->string('name');
            $table->string('ip_address');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
