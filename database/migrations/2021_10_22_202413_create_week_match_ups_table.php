<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWeekMatchUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('week_match_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("week_id");
            $table->unsignedInteger("opponent_one_id");
            $table->unsignedInteger("opponent_two_id");
            $table->unsignedInteger("opponent_one_score")->default(0);
            $table->unsignedInteger("opponent_two_score")->default(0);
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
        Schema::dropIfExists('week_match_ups');
    }
}
