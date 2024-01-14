<?php

namespace App\Repositories;

use App\Interfaces\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $project)
    {
        parent::__construct($project);
    }

    public function paginateMembers(Project $project, int $perPage): LengthAwarePaginator
    {
        return $project->members()
            ->with('team')
            ->orderBy('members.id')
            ->paginate($perPage);
    }
}
