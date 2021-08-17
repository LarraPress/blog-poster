<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitColumnInScrapingJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraping_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('daily_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraping_jobs', function (Blueprint $table) {
            $table->dropColumn('daily_limit');
        });
    }
}
