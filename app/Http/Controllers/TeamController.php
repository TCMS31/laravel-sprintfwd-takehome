<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Http\Resources\MemberResource;
use App\Http\Resources\TeamResource;
use App\Interfaces\TeamRepositoryInterface;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends BaseController
{
    public function __construct(private readonly TeamRepositoryInterface $teams)
    {
        parent::__construct($teams);
    }

    protected function resourceClass(): string
    {
        return TeamResource::class;
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {
        $team = $this->teams->store($request->validated());

        return $this->resource($team)->response()->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function update(UpdateTeamRequest $request, int $id): JsonResponse
    {
        return $this->resource($this->teams->update($request->validated(), $id))->response();
    }

    /** GET /api/teams/{team}/members */
    public function members(Request $request, Team $team): JsonResponse
    {
        $members = $this->teams->paginateMembers($team, $this->perPage($request));

        return MemberResource::collection($members)->response();
    }
}
