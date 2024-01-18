<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Exercises the team endpoints through the real container: routes, form
 * requests, repositories, Eloquent and the schema. Nothing is mocked, so a
 * regression anywhere in that stack fails here.
 */
class TeamApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_teams_paginated(): void
    {
        Team::factory()->count(3)->create();

        $this->getJson('/api/teams')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'created_at']], 'meta' => ['total', 'per_page']])
            ->assertJsonPath('meta.total', 3);
    }

    public function test_index_caps_per_page_so_a_client_cannot_pull_the_whole_table(): void
    {
        Team::factory()->count(3)->create();

        $this->getJson('/api/teams?per_page=100000')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_it_creates_a_team(): void
    {
        $this->postJson('/api/teams', ['name' => 'Platform'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Platform');

        $this->assertDatabaseHas('teams', ['name' => 'Platform']);
    }

    public function test_it_rejects_a_team_without_a_name(): void
    {
        $this->postJson('/api/teams', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseCount('teams', 0);
    }

    public function test_it_rejects_a_duplicate_team_name(): void
    {
        Team::factory()->create(['name' => 'Platform']);

        $this->postJson('/api/teams', ['name' => 'Platform'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_it_shows_a_team(): void
    {
        $team = Team::factory()->create(['name' => 'Growth']);

        $this->getJson("/api/teams/{$team->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $team->id)
            ->assertJsonPath('data.name', 'Growth');
    }

    public function test_it_returns_json_404_for_an_unknown_team(): void
    {
        $this->getJson('/api/teams/9999')
            ->assertNotFound()
            ->assertJsonPath('message', 'Resource not found.');
    }

    public function test_it_updates_a_team(): void
    {
        $team = Team::factory()->create(['name' => 'Old']);

        $this->putJson("/api/teams/{$team->id}", ['name' => 'New'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'New']);
    }

    public function test_updating_a_team_to_its_own_name_is_allowed(): void
    {
        $team = Team::factory()->create(['name' => 'Same']);

        $this->putJson("/api/teams/{$team->id}", ['name' => 'Same'])->assertOk();
    }

    public function test_it_deletes_a_team(): void
    {
        $team = Team::factory()->create();

        $this->deleteJson("/api/teams/{$team->id}")->assertNoContent();

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_it_lists_the_members_of_a_team_and_not_other_teams_members(): void
    {
        $team = Team::factory()->create();
        $other = Team::factory()->create();
        $mine = Member::factory()->count(2)->create(['team_id' => $team->id]);
        Member::factory()->count(3)->create(['team_id' => $other->id]);

        $response = $this->getJson("/api/teams/{$team->id}/members")
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $this->assertEqualsCanonicalizing(
            $mine->pluck('id')->all(),
            collect($response->json('data'))->pluck('id')->all(),
        );
    }

    public function test_members_of_an_unknown_team_is_a_404_not_an_empty_list(): void
    {
        $this->getJson('/api/teams/9999/members')->assertNotFound();
    }
}
