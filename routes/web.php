<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', 'TeamController@index')->name('get.homepage');
Route::get('/team', 'TeamController@index')->name('get.team.index');
Route::get('/seed', 'TeamController@seed')->name('get.fixture.seed');

Route::get('/fixture', 'FixtureController@index')->name('get.fixture.index');
Route::get('/fixture/create', 'FixtureController@generateFixture')->name('get.fixture.create');
Route::get('/fixture/play/all', 'FixtureController@playAll')->name('get.fixture.play.all');
Route::get('/fixture/play/{weekId}', 'FixtureController@playWeekById')->name('get.fixture.play.week');
Route::post('/fixture/reset', 'FixtureController@resetData')->name('post.reset.data');


Route::get('/point-table', 'PointTableController@index')->name('get.point.table');
