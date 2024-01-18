<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The one piece of real domain logic in the app: assigning a member to a
 * project exactly once.
 */
class ProjectMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_member_to_a_project(): void
    {
        $project = Project::factory()->create();
        $member = Member::factory()->create();

        $this->postJson("/api/projects/{$project->id}/members/{$member->id}")
            ->assertCreated()
            ->assertJsonPath('message', 'Member added to project.');

        $this->assertDatabaseHas('project_member', [
            'project_id' => $project->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_adding_the_same_member_twice_is_a_conflict_and_creates_one_row(): void
    {
        $project = Project::factory()->create();
        $member = Member::factory()->create();

        $this->postJson("/api/projects/{$project->id}/members/{$member->id}")->assertCreated();

        $this->postJson("/api/projects/{$project->id}/members/{$member->id}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'Member is already assigned to this project.');

        $this->assertDatabaseCount('project_member', 1);
    }

    public function test_adding_a_member_to_an_unknown_project_returns_404(): void
    {
        $member = Member::factory()->create();

        $this->postJson("/api/projects/9999/members/{$member->id}")->assertNotFound();

        $this->assertDatabaseCount('project_member', 0);
    }

    public function test_adding_an_unknown_member_to_a_project_returns_404(): void
    {
        $project = Project::factory()->create();

        $this->postJson("/api/projects/{$project->id}/members/9999")->assertNotFound();

        $this->assertDatabaseCount('project_member', 0);
    }

    public function test_a_member_can_be_on_several_projects(): void
    {
        $member = Member::factory()->create();
        $a = Project::factory()->create();
        $b = Project::factory()->create();

        $this->postJson("/api/projects/{$a->id}/members/{$member->id}")->assertCreated();
        $this->postJson("/api/projects/{$b->id}/members/{$member->id}")->assertCreated();

        $this->assertDatabaseCount('project_member', 2);
    }

    public function test_the_schema_refuses_a_duplicate_membership_row(): void
    {
        $project = Project::factory()->create();
        $member = Member::factory()->create();

        $project->members()->attach($member);

        $this->expectException(\Illuminate\Database\QueryException::class);

        $project->members()->attach($member);
    }
}
