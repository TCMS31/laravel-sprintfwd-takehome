<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed a small but realistic org: a handful of teams, members spread
     * across them, and projects staffed from the whole member pool.
     */
    public function run(): void
    {
        $teams = Team::factory()->count(4)->create();

        $members = $teams->flatMap(
            fn (Team $team) => Member::factory()->count(5)->create(['team_id' => $team->id]),
        );

        Project::factory()->count(3)->create()->each(
            fn (Project $project) => $project->members()->syncWithoutDetaching(
                $members->random(6)->pluck('id')->all(),
            ),
        );
    }
}
