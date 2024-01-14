<?php

namespace App\Repositories;

use App\Interfaces\ProjectMemberRepositoryInterface;
use App\Models\Member;
use App\Models\Project;

class ProjectMemberRepository implements ProjectMemberRepositoryInterface
{
    public function addMemberToProject(Project $project, Member $member): void
    {
        // syncWithoutDetaching is idempotent, so a lost race against the
        // service-level duplicate check cannot produce a second pivot row.
        $project->members()->syncWithoutDetaching([$member->getKey()]);
    }

    public function isMemberInProject(Project $project, Member $member): bool
    {
        return $project->members()
            ->whereKey($member->getKey())
            ->exists();
    }
}
