<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Week;
use App\Models\WeekMatchUp;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;

class FixtureController extends Controller
{
    public function index()
    {
        $allFixturesData = WeekMatchUp::getAllFixtures();

        $allFixtures = [];
        foreach ($allFixturesData as $key => $value) {
            $allFixtures[$value->week_id][] = $value;
        }

        $teams = Team::all();
        $pointTables = Team::getPointTable();
        $championshipChances = $this->calculateChampionsipChance();
        
        return view('fixture.index',  compact('allFixtures', 'teams', 'pointTables', 'championshipChances'));
    }

    public function generateFixture()
    {
        WeekMatchUp::truncate();
        $teams = Team::getTeams();
        Week::createAllWeeks();
        $weeks = Week::getHalfWeeks();

        while (count($teams) > 0) {
            $team = $teams->random(1)->first();
            $otherTeams =  $teams->filter(function ($element) use ($team) {
                return (($element->id != $team->id));
            });

            while (count($otherTeams) > 0) {
                $opponent = $otherTeams->random(1)->first();
                $playWeek = null;

                while (is_null($playWeek)) {

                    $week = $weeks->random(1)->first();
                    $weekMatch = WeekMatchUp::getOpponentMatchUp($week->id, $team->id, $opponent->id);
                    if (!is_null($weekMatch)) {
                        $weeks = $weeks->filter(function ($element) use ($week) {
                            return ($element->id != $week->id);
                        });
                        continue;
                    }
                    $playWeek = $week;
                }

                $previousMatchup = WeekMatchUp::getPreviousMathcup($team->id, $opponent->id);

                if (is_null($previousMatchup)) {
                    $homeSideId = $team->id;
                    $awaySideId = $opponent->id;

                    $dice = random_int(0, 1);
                    if ($dice == 1) // dicing for home&away
                    {
                        $homeSideId = $opponent->id;
                        $awaySideId = $team->id;
                    }
                    WeekMatchUp::addWeekMatchUp($playWeek->id, $homeSideId, $awaySideId);
                }

                $weeks = Week::getHalfWeeks();
                $otherTeams =  $otherTeams->filter(function ($element) use ($opponent) {
                    return (($element->id != $opponent->id));
                });
            }

            $teams =  $teams->filter(function ($element) use ($team) {
                return (($element->id != $team->id));
            });
            $weeks = Week::getHalfWeeks();
        }

        $this->addAways();
        return Redirect::route('get.fixture.index');
    }

    private function addAways()
    {
        $teamsCount = Team::getTeams()->count();
        $awayCount = $teamsCount - 1;

        $allFixtures = WeekMatchUp::all();
        foreach ($allFixtures as $fixture) {
            $secondHalfWeekId = $fixture->week_id + $awayCount;
            WeekMatchUp::addWeekMatchUp($secondHalfWeekId, $fixture->opponent_two_id, $fixture->opponent_one_id);
        }
    }

    public function playAll()
    {
        $weeks = Week::all();
        foreach ($weeks as $week) {
            $this->playWeek($week);
        }
        return "OK";
    }
    public function playWeekById($weekId)
    {
        $week = Week::find($weekId);
        $error = $this->playWeek($week);

        if ($error != "OK")
            return redirect()->back()->withErrors($error);

        return Redirect::back();
    }

    public function playWeek($week)
    {
        if (is_null($week))
            return "Fixture completed";

        if ($week->is_played)
            return $week->name .  " already played";

        $fixtures = WeekMatchUp::getMatchesByWeekId($week->id);
        foreach ($fixtures as $fixture) {
            $this->playMatch($fixture);
        }

        $week->is_played = true;
        $week->save();

        return "OK";
    }

    private function playMatch($matchUp)
    {
        $attackCount = 4; // this is how many opportunity in a match
        $home = $matchUp->home; // home team
        $away = $matchUp->away; // home team

        $score["home"] = 0;
        $score["away"] = 0;

        // if has no win so far , I give it a chance
        $homeWin = $home->win == 0 ? 1 : $home->win;
        $awayWin = $away->win == 0 ? 1 : $away->win; // I will add 10% of opponent win count if I have time

        $totalChance = $homeWin + $awayWin; // this is for who is favorite in game

        for ($i = 0; $i < $attackCount; $i++) {

            $whoScoredDice = random_int(0, $totalChance);
            if ($whoScoredDice <= $home->win)
                $score["home"]++;
            else
                $score["away"]++;
        }

        $matchUp->opponent_one_score = $score["home"];
        $matchUp->opponent_two_score = $score["away"];
        $matchUp->save();

        $this->saveMatchResult($home, $away, $score);
    }

    public function saveMatchResult($home, $away, $score)
    {

        $home->played++;
        $home->goal_scored += $score["home"];
        $home->conceded_goal += $score["away"];
        $home->goal_difference =  $home->goal_scored -  $home->conceded_goal;

        $away->played++;
        $away->goal_scored += $score["away"];
        $away->conceded_goal += $score["home"];
        $away->goal_difference =  $away->goal_scored -  $away->conceded_goal;

        if ($score["home"] > $score["away"]) {
            $home->win++;
            $home->point += 3;
            $away->lose++;
        } else if ($score["home"] < $score["away"]) {
            $away->win++;
            $away->point += 3;
            $home->lose++;
        } else {
            $away->draw++;
            $away->point++;
            $home->draw++;
            $home->point++;
        }

        $home->save();
        $away->save();
    }

    public function calculateChampionsipChance()
    {
        $unplayedWeeks = Week::getUnplayedWeeks();
        $unplayedWeekCount = $unplayedWeeks->count();

        $leaderPoint = 0;
        $chances = [];
        $potentielPoint = $unplayedWeekCount * 3; // every win
        $canditateTeams = [];
        $pointTables = Team::getPointTable();

        $i = 0;
        foreach ($pointTables as $pointTable) {
            $i++;
            $chances[$pointTable->name] = 0;
            if ($leaderPoint == 0) {
                $leaderPoint = $pointTable->point;
                $canditateTeams[] = $pointTable;
                continue;
            }
            $pointDiff = $leaderPoint -  $pointTable->point;

            if ($pointDiff < $potentielPoint)
                $canditateTeams[] = $pointTable;

            $chanceOfWinAvg = ($pointDiff / $potentielPoint) * 25;

            $chances[$pointTable->name] = (-1 * $chanceOfWinAvg); // this is for negative avg
        }

        asort($chances); // sorting by keeping keys
        $clonedChances = unserialize(serialize($chances)); // deep copy of chances array

        while (count($clonedChances) > 0) {
            $keys = array_keys($clonedChances);
            $key = $keys[0];
            $chance =  $clonedChances[$key];
            unset($clonedChances[$key]);

            if (count($clonedChances) == 0)
                $pointToIncrease = abs($chance);
            else
                $pointToIncrease = abs($chance) / count($clonedChances);

            foreach ($clonedChances as $clonedChanceKey => $clonedChanceValue) {
                if ($chance == $clonedChanceValue) // do not increase if has same disadvantage
                {
                    $pointToIncrease += $pointToIncrease;
                    continue;
                }
                $chances[$clonedChanceKey] += $pointToIncrease;
            }
        }

        // chances with point table calculation
        foreach ($canditateTeams as $canditateTeam) {
            $changeDataRegardingToPoint = 100 / count($canditateTeams);
            $chances[$canditateTeam->name] +=  $changeDataRegardingToPoint;
            $chances[$canditateTeam->name] = number_format($chances[$canditateTeam->name], 2, ".", "");
        }

        arsort($chances);
        return $chances;
    }

    public function resetData()
    {
        Artisan::call('db:wipe');
        Artisan::call('migrate');
        Artisan::call('db:seed');
        return Redirect::route('get.team.index');
    }
}
