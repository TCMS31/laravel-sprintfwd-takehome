<?php

namespace App\Services;

use App\Interfaces\MemberRepositoryInterface;
use App\Interfaces\ProjectMemberRepositoryInterface;
use App\Models\Member;
use App\Models\Project;

/**
 * Rules that span more than one aggregate.
 *
 * "Is this member already on the project?" and "move this member to another
 * team" are decisions about the domain, not about HTTP, so they live here
 * rather than in a controller.
 */
class MembershipService
{
    public function __construct(
        private readonly MemberRepositoryInterface $members,
        private readonly ProjectMemberRepositoryInterface $projectMembers,
    ) {
    }

    /**
     * Assign a member to a project.
     *
     * @return bool true when the member was added, false when they were
     *              already on the project (the caller maps this to 409)
     */
    public function addMemberToProject(Project $project, Member $member): bool
    {
        if ($this->projectMembers->isMemberInProject($project, $member)) {
            return false;
        }

        $this->projectMembers->addMemberToProject($project, $member);

        return true;
    }

    /** Move a member to another team. Null means no such member. */
    public function moveMemberToTeam(int $memberId, int $teamId): ?Member
    {
        return $this->members->updateTeam($memberId, $teamId);
    }
}
