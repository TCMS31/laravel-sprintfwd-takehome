<?php

namespace App\Interfaces;

use App\Models\Member;

interface MemberRepositoryInterface extends BaseRepositoryInterface
{
    /** Move a member to another team. Returns null when the member does not exist. */
    public function updateTeam(int $id, int $teamId): ?Member;
}
