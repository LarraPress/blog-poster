<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostIdentifierInListColumnToScrapingJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scraping_jobs', function (Blueprint $table) {
            $table->string('identifier_in_list', 1000);
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
            $table->dropColumn('identifier_in_list');
        });
    }
}
