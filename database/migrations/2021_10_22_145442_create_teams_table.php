<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string("name", 30);
            $table->unsignedInteger("played")->default(0);
            $table->unsignedInteger("point")->default(0);
            $table->unsignedInteger("win")->default(0);
            $table->unsignedInteger("draw")->default(0);
            $table->unsignedInteger("lose")->default(0);
            $table->unsignedInteger("goal_scored")->default(0);
            $table->unsignedInteger("conceded_goal")->default(0);
            $table->integer("goal_difference")->default(0);
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
        Schema::dropIfExists('teams');
    }
}
