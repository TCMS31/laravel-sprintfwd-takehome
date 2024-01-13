<?php

namespace App\Providers;

use App\Interfaces\MemberRepositoryInterface;
use App\Interfaces\ProjectMemberRepositoryInterface;
use App\Interfaces\ProjectRepositoryInterface;
use App\Interfaces\TeamRepositoryInterface;
use App\Repositories\MemberRepository;
use App\Repositories\ProjectMemberRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TeamRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Binds each persistence contract to its Eloquent implementation.
 *
 * BaseRepositoryInterface is intentionally absent: BaseRepository is abstract
 * and needs a concrete model, so binding it produced an unresolvable entry.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        TeamRepositoryInterface::class => TeamRepository::class,
        ProjectRepositoryInterface::class => ProjectRepository::class,
        MemberRepositoryInterface::class => MemberRepository::class,
        ProjectMemberRepositoryInterface::class => ProjectMemberRepository::class,
    ];
}
