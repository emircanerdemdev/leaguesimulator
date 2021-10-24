<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeekMatchUp extends Model
{
    protected $table = "week_match_ups";

    protected $fillable = [
        'week_id', 'opponent_one_id', 'opponent_two_id', 'opponent_one_score', 'opponent_two_score'
    ];

    public function week()
    {
        return $this->belongsTo(Week::class, "week_id", "id");
    }

    public function home()
    {
        return $this->belongsTo(Team::class, "opponent_one_id", "id");
    }

    public function away()
    {
        return $this->belongsTo(Team::class, "opponent_two_id", "id");
    }


    public static function getMatchesByWeekId($weekId)
    {
        return self::with(["home", "away"])->where("week_id", $weekId)->get();
    }

    public static function getAllFixtures()
    {
        return self::with(["week", "home", "away"])->orderBy("week_id")->get();
    }
    

    public static function isMatchupExist($opponentOneId, $opponentTwoId)
    {
        return self::where("opponent_one_id", $opponentOneId)->where("opponent_two_id",  $opponentTwoId)->first();
    }

    public static function getOpponentMatchUp($weekId, $opponentOneId, $opponentTwoId)
    {
        return self::where("week_id", $weekId)->where(function ($query) use ($opponentOneId, $opponentTwoId) {
            $query->where('opponent_one_id', $opponentOneId)
                ->orWhere('opponent_two_id',  $opponentOneId)
                ->orWhere('opponent_one_id', $opponentTwoId)
                ->orWhere('opponent_two_id',  $opponentTwoId);
        })->first();
    }

    public static function getPreviousMathcup($opponentOneId, $opponentTwoId)
    {
        return self::where(function ($query) use ($opponentOneId) {
            $query->where('opponent_one_id', $opponentOneId)
                ->orWhere('opponent_two_id',  $opponentOneId);
        })->where(function ($query) use ($opponentTwoId) {
            $query->where('opponent_one_id', $opponentTwoId)
                ->orWhere('opponent_two_id',  $opponentTwoId);
        })->first();
    }


    public static function addWeekMatchUp($weekId, $opponentOneId, $opponentTwoId)
    {
        $exist = self::isMatchupExist($opponentOneId, $opponentTwoId);
        if (!is_null($exist))
            return null;
        $matchUp = new self();
        $matchUp->week_id = $weekId;
        $matchUp->opponent_one_id = $opponentOneId;
        $matchUp->opponent_two_id = $opponentTwoId;
        $matchUp->save();
        return $matchUp;
    }
}
