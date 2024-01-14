<?php

namespace App\Repositories;

use App\Interfaces\MemberRepositoryInterface;
use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    public function __construct(Member $member)
    {
        parent::__construct($member);
    }

    /**
     * Members are almost always rendered with their team, so eager load it
     * here rather than letting the serializer trigger one query per row.
     */
    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('team')
            ->orderBy($this->model->getKeyName())
            ->paginate($perPage);
    }

    public function updateTeam(int $id, int $teamId): ?Member
    {
        /** @var Member|null $member */
        $member = $this->model->newQuery()->find($id);

        if ($member === null) {
            return null;
        }

        $member->update(['team_id' => $teamId]);

        return $member->refresh()->load('team');
    }
}
