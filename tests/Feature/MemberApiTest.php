<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemberApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_member(): void
    {
        $team = Team::factory()->create();

        $this->postJson('/api/members', [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'city' => 'London',
            'team_id' => $team->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.first_name', 'Ada')
            ->assertJsonPath('data.team.id', $team->id);

        $this->assertDatabaseHas('members', ['first_name' => 'Ada', 'team_id' => $team->id]);
    }

    public function test_it_rejects_a_member_with_a_team_that_does_not_exist(): void
    {
        $this->postJson('/api/members', [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'team_id' => 9999,
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('team_id');

        $this->assertDatabaseCount('members', 0);
    }

    public function test_it_rejects_a_member_with_no_team(): void
    {
        $this->postJson('/api/members', ['first_name' => 'Ada', 'last_name' => 'Lovelace'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('team_id');
    }

    public function test_it_shows_a_member_with_their_team(): void
    {
        $member = Member::factory()->create();

        $this->getJson("/api/members/{$member->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $member->id)
            ->assertJsonPath('data.team.id', $member->team_id);
    }

    public function test_it_updates_a_member(): void
    {
        $member = Member::factory()->create(['city' => 'London']);

        $this->putJson("/api/members/{$member->id}", ['city' => 'Berlin'])
            ->assertOk()
            ->assertJsonPath('data.city', 'Berlin');

        $this->assertDatabaseHas('members', ['id' => $member->id, 'city' => 'Berlin']);
    }

    public function test_it_deletes_a_member(): void
    {
        $member = Member::factory()->create();

        $this->deleteJson("/api/members/{$member->id}")->assertNoContent();

        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function test_it_moves_a_member_to_another_team(): void
    {
        $from = Team::factory()->create();
        $to = Team::factory()->create();
        $member = Member::factory()->create(['team_id' => $from->id]);

        $this->patchJson("/api/members/{$member->id}/team", ['team_id' => $to->id])
            ->assertOk()
            ->assertJsonPath('data.team_id', $to->id)
            ->assertJsonPath('data.team.id', $to->id);

        $this->assertDatabaseHas('members', ['id' => $member->id, 'team_id' => $to->id]);
    }

    public function test_moving_a_member_to_an_unknown_team_is_rejected_and_changes_nothing(): void
    {
        $from = Team::factory()->create();
        $member = Member::factory()->create(['team_id' => $from->id]);

        $this->patchJson("/api/members/{$member->id}/team", ['team_id' => 9999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('team_id');

        $this->assertDatabaseHas('members', ['id' => $member->id, 'team_id' => $from->id]);
    }

    public function test_moving_an_unknown_member_returns_404(): void
    {
        $team = Team::factory()->create();

        $this->patchJson('/api/members/9999/team', ['team_id' => $team->id])
            ->assertNotFound()
            ->assertJsonPath('message', 'Member not found.');
    }

    /**
     * Guards the eager load in MemberRepository::paginate().
     *
     * Two things are asserted together on purpose. The serialiser uses
     * whenLoaded(), so dropping the eager load does not produce an N+1 - it
     * silently drops `team` from every row instead. The query-count bound
     * catches the opposite mistake: swapping whenLoaded() for a direct
     * relation access, which would give one query per member.
     */
    public function test_listing_members_includes_each_team_in_a_bounded_number_of_queries(): void
    {
        $team = Team::factory()->create();
        Member::factory()->count(10)->create(['team_id' => $team->id]);

        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->getJson('/api/members?per_page=10')
            ->assertOk()
            ->assertJsonCount(10, 'data');

        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        foreach ($response->json('data') as $row) {
            $this->assertArrayHasKey('team', $row, 'A member row was serialised without its team.');
            $this->assertSame($team->id, $row['team']['id']);
        }

        $this->assertLessThanOrEqual(
            4,
            $queries,
            "Listing 10 members issued {$queries} queries; the team relation is not eager loaded.",
        );
    }
}
