<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Week;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::all();
        return view('team.index' , compact("teams"));
    }
    public function seed()
    {
        Artisan::call("db:seed");
        Week::createAllWeeks();
        return Redirect::back();
    }
    
}
