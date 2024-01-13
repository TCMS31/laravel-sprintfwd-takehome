<?php

namespace App\Interfaces;

use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TeamRepositoryInterface extends BaseRepositoryInterface
{
    /** Page through the members of a team. */
    public function paginateMembers(Team $team, int $perPage): LengthAwarePaginator;
}
