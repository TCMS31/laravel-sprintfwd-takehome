<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Requests\UpdateMemberTeamRequest;
use App\Http\Resources\MemberResource;
use App\Interfaces\MemberRepositoryInterface;
use App\Services\MembershipService;
use Illuminate\Http\JsonResponse;

class MemberController extends BaseController
{
    public function __construct(
        private readonly MemberRepositoryInterface $members,
        private readonly MembershipService $membership,
    ) {
        parent::__construct($members);
    }

    protected function resourceClass(): string
    {
        return MemberResource::class;
    }

    public function show(int $id): JsonResponse
    {
        return $this->resource($this->members->find($id)->load('team'))->response();
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = $this->members->store($request->validated());

        return $this->resource($member->load('team'))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    public function update(UpdateMemberRequest $request, int $id): JsonResponse
    {
        $member = $this->members->update($request->validated(), $id);

        return $this->resource($member->load('team'))->response();
    }

    /** PATCH /api/members/{id}/team */
    public function updateTeam(UpdateMemberTeamRequest $request, int $id): JsonResponse
    {
        $member = $this->membership->moveMemberToTeam($id, (int) $request->validated('team_id'));

        if ($member === null) {
            return response()->json(
                ['message' => 'Member not found.'],
                JsonResponse::HTTP_NOT_FOUND,
            );
        }

        return $this->resource($member)->response();
    }
}
