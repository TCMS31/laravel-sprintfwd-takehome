<?php

namespace Tests\Unit;

use App\Interfaces\MemberRepositoryInterface;
use App\Interfaces\ProjectMemberRepositoryInterface;
use App\Models\Member;
use App\Models\Project;
use App\Services\MembershipService;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

/**
 * True unit test: the service's decision logic in isolation from the database.
 * The integration tests in tests/Feature cover the same paths end to end.
 */
class MembershipServiceTest extends TestCase
{
    private MockInterface $members;

    private MockInterface $projectMembers;

    private MembershipService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->members = Mockery::mock(MemberRepositoryInterface::class);
        $this->projectMembers = Mockery::mock(ProjectMemberRepositoryInterface::class);
        $this->service = new MembershipService($this->members, $this->projectMembers);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_adds_a_member_that_is_not_yet_on_the_project(): void
    {
        $project = new Project;
        $member = new Member;

        $this->projectMembers->shouldReceive('isMemberInProject')
            ->once()->with($project, $member)->andReturnFalse();
        $this->projectMembers->shouldReceive('addMemberToProject')
            ->once()->with($project, $member);

        $this->assertTrue($this->service->addMemberToProject($project, $member));
    }

    public function test_it_does_not_write_when_the_member_is_already_on_the_project(): void
    {
        $project = new Project;
        $member = new Member;

        $this->projectMembers->shouldReceive('isMemberInProject')
            ->once()->with($project, $member)->andReturnTrue();
        $this->projectMembers->shouldNotReceive('addMemberToProject');

        $this->assertFalse($this->service->addMemberToProject($project, $member));
    }

    public function test_it_returns_null_when_moving_a_member_that_does_not_exist(): void
    {
        $this->members->shouldReceive('updateTeam')->once()->with(42, 7)->andReturnNull();

        $this->assertNull($this->service->moveMemberToTeam(42, 7));
    }

    public function test_it_returns_the_moved_member(): void
    {
        $member = new Member;

        $this->members->shouldReceive('updateTeam')->once()->with(42, 7)->andReturn($member);

        $this->assertSame($member, $this->service->moveMemberToTeam(42, 7));
    }
}
