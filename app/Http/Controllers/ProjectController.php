<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\ProjectResource;
use App\Interfaces\ProjectRepositoryInterface;
use App\Models\Member;
use App\Models\Project;
use App\Services\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends BaseController
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projects,
        private readonly MembershipService $membership,
    ) {
        parent::__construct($projects);
    }

    protected function resourceClass(): string
    {
        return ProjectResource::class;
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projects->store($request->validated());

        return $this->resource($project)->response()->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function update(UpdateProjectRequest $request, int $id): JsonResponse
    {
        return $this->resource($this->projects->update($request->validated(), $id))->response();
    }

    /** GET /api/projects/{project}/members */
    public function members(Request $request, Project $project): JsonResponse
    {
        $members = $this->projects->paginateMembers($project, $this->perPage($request));

        return MemberResource::collection($members)->response();
    }

    /** POST /api/projects/{project}/members/{member} */
    public function addMember(Project $project, Member $member): JsonResponse
    {
        $added = $this->membership->addMemberToProject($project, $member);

        if (! $added) {
            return response()->json(
                ['message' => 'Member is already assigned to this project.'],
                JsonResponse::HTTP_CONFLICT,
            );
        }

        return response()->json(
            ['message' => 'Member added to project.'],
            JsonResponse::HTTP_CREATED,
        );
    }
}
