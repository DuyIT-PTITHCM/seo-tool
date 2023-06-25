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
        Schema::create('measurements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('rankings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('measurement_id');
            $table->foreign('measurement_id')->references('id')->on('measurements')->onDelete('cascade');
            $table->string('keyword')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('search_engine')->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->integer('rank');
            $table->string('search_results_link')->nullable()->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rankings');
        Schema::dropIfExists('measurements');
    }
};
