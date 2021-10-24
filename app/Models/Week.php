<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    const WEEK_NAME_KEY = 'Week';

    protected $table = "weeks";

    protected $fillable = [
        'name', 'is_played'
    ];


    public static function getHalfWeeks()
    {
        $allWeeks = self::all();
        $halfCount = count($allWeeks) / 2; // its filtering second half;
        return $allWeeks->take($halfCount);
    }

    public static function createWeeks($weekCount)
    {
        self::truncate(); // clear exist data

        for ($i = 0; $i < $weekCount; $i++)
            self::create(["name" => self::WEEK_NAME_KEY . " " . ($i + 1)]);

        $weeks = self::all();
        return $weeks;
    }

    public static function createAllWeeks()
    {
        $countOfFixture = WeekMatchUp::all()->count();
        $weeks = [];
        if ($countOfFixture == 0) {
            $teams = Team::getTeams();
            $weekCount = (count($teams) - 1); // its for removing own. For example, if there is 4 team, 3 weeks will be played
            $weekCount = $weekCount * 2; // This is for the second half of the season       
            $weeks = self::createWeeks($weekCount);
        }

        if (count($weeks) == 0)
            $weeks = self::all();

        return $weeks;
    }

    public static function getUnplayedWeeks()
    {
        return self::where("is_played", "=", 0)->get();
    }
}
