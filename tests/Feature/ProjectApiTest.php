<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_project(): void
    {
        $this->postJson('/api/projects', ['name' => 'Apollo'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Apollo');

        $this->assertDatabaseHas('projects', ['name' => 'Apollo']);
    }

    public function test_it_rejects_a_project_without_a_name(): void
    {
        $this->postJson('/api/projects', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_it_lists_projects_paginated(): void
    {
        Project::factory()->count(2)->create();

        $this->getJson('/api/projects')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);
    }

    public function test_it_shows_and_updates_and_deletes_a_project(): void
    {
        $project = Project::factory()->create(['name' => 'Apollo']);

        $this->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Apollo');

        $this->putJson("/api/projects/{$project->id}", ['name' => 'Gemini'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Gemini');

        $this->deleteJson("/api/projects/{$project->id}")->assertNoContent();

        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    }

    public function test_deleting_a_project_removes_its_membership_rows(): void
    {
        $project = Project::factory()->create();
        $member = Member::factory()->create();
        $project->members()->attach($member);

        $this->deleteJson("/api/projects/{$project->id}")->assertNoContent();

        $this->assertDatabaseCount('project_member', 0);
    }

    public function test_it_lists_only_the_members_of_that_project(): void
    {
        $team = Team::factory()->create();
        $project = Project::factory()->create();
        $other = Project::factory()->create();

        $mine = Member::factory()->count(2)->create(['team_id' => $team->id]);
        $theirs = Member::factory()->count(3)->create(['team_id' => $team->id]);

        $project->members()->attach($mine);
        $other->members()->attach($theirs);

        $response = $this->getJson("/api/projects/{$project->id}/members")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->assertEqualsCanonicalizing(
            $mine->pluck('id')->all(),
            collect($response->json('data'))->pluck('id')->all(),
        );
    }

    public function test_members_of_an_unknown_project_returns_404(): void
    {
        $this->getJson('/api/projects/9999/members')->assertNotFound();
    }
}
