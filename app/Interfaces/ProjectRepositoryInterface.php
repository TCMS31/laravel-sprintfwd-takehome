<?php

namespace App\Interfaces;

use App\Models\Project;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface extends BaseRepositoryInterface
{
    /** Page through the members assigned to a project. */
    public function paginateMembers(Project $project, int $perPage): LengthAwarePaginator;
}
