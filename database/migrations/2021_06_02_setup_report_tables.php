<?php namespace Thoughtco\Reports\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReportTables extends Migration
{
    public function up()
    {
        Schema::create('thoughtco_reportbuilder', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('title');
            $table->mediumText('builderjson');
            $table->mediumText('list_columns');
            $table->mediumText('csv_columns');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('thoughtco_reportbuilder');
    }
}
