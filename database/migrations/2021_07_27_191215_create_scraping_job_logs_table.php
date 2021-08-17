<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScrapingJobLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scraping_job_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scraping_job_id')->references('id')->on('scraping_jobs')->cascadeOnDelete();
            $table->unsignedTinyInteger('status');
            $table->string('source_url', 1000);
            $table->text('log')->nullable();
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
        Schema::dropIfExists('scraping_job_logs');
    }
}
