<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Persistence contract shared by every resource repository.
 *
 * Controllers depend on this interface, never on Eloquent directly, so the
 * storage engine can be swapped without touching the HTTP layer.
 */
interface BaseRepositoryInterface
{
    /**
     * Return a page of records. Always paginated: an unbounded `all()` is the
     * first thing to fall over once a table grows.
     */
    public function paginate(int $perPage): LengthAwarePaginator;

    public function store(array $data): Model;

    /** @throws \Illuminate\Database\Eloquent\ModelNotFoundException */
    public function find(int $id): Model;

    public function update(array $data, int $id): Model;

    public function delete(int $id): void;
}
