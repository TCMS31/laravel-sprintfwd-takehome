<?php

namespace App\Repositories;

use App\Interfaces\TeamRepositoryInterface;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $team)
    {
        parent::__construct($team);
    }

    public function paginateMembers(Team $team, int $perPage): LengthAwarePaginator
    {
        return $team->members()
            ->orderBy('id')
            ->paginate($perPage);
    }
}
