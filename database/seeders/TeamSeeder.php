<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $firstTeam = "Tottenham";
        $secondTeam = "Newcastle";
        $thirdTeam = "Norwich";
        $forthTeam = "Leicester";

        $exist = Team::where("name", "=", $firstTeam)->first();
        if (is_null($exist))
            Team::create(["name" => $firstTeam]);

        $exist = Team::where("name", "=", $secondTeam)->first();
        if (is_null($exist))
            Team::create(["name" => $secondTeam]);

        $exist = Team::where("name", "=", $thirdTeam)->first();
        if (is_null($exist))
            Team::create(["name" => $thirdTeam]);

        $exist = Team::where("name", "=", $forthTeam)->first();
        if (is_null($exist))
            Team::create(["name" => $forthTeam]);
    }
}
