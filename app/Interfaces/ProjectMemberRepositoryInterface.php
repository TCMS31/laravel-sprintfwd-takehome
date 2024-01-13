<?php

namespace App\Interfaces;

use App\Models\Member;
use App\Models\Project;

/**
 * The project/member join is its own concern: it owns no model of its own,
 * only the pivot rows between two aggregates.
 */
interface ProjectMemberRepositoryInterface
{
    public function addMemberToProject(Project $project, Member $member): void;

    public function isMemberInProject(Project $project, Member $member): bool;
}
