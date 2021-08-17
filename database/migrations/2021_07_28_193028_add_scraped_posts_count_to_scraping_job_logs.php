<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScrapedPostsCountToScrapingJobLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraping_job_logs', function (Blueprint $table) {
            $table->unsignedInteger('scraped_posts_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scraping_job_logs', function (Blueprint $table) {
            $table->dropColumn('scraped_posts_count');
        });
    }
}
