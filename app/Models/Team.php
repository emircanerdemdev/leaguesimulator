<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
  protected $table = "teams";

  protected $fillable = [
    'name', 'played', 'point', 'win', 'draw', 'lose', 'goal_difference'
  ];

  public static function getTeams()
  {
    return self::all();
  }

  public static function getPointTable()
  {
    $pointTable = [];
    $firstRecord = self::first();
    if ($firstRecord->played == 0)
      $pointTable = self::orderBy("name", "asc")->get();
    else
      $pointTable = self::orderBy("point", "desc")->get();

    return $pointTable;
  }
}
