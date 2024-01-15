<?php

use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Loaded by RouteServiceProvider under the "/api" prefix and the "api"
| middleware group: stateless, rate limited, with route-model binding. These
| endpoints previously lived in routes/web.php, which put a JSON API behind
| session state and CSRF verification.
|
*/

Route::apiResource('teams', TeamController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('projects', ProjectController::class);

Route::get('teams/{team}/members', [TeamController::class, 'members'])
    ->whereNumber('team')
    ->name('teams.members');

Route::get('projects/{project}/members', [ProjectController::class, 'members'])
    ->whereNumber('project')
    ->name('projects.members');

Route::post('projects/{project}/members/{member}', [ProjectController::class, 'addMember'])
    ->whereNumber(['project', 'member'])
    ->name('projects.members.store');

Route::patch('members/{member}/team', [MemberController::class, 'updateTeam'])
    ->whereNumber('member')
    ->name('members.team.update');
