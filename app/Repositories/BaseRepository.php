<?php

namespace App\Repositories;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Generic Eloquent implementation of {@see BaseRepositoryInterface}.
 *
 * Abstract on purpose: it is only ever used through a concrete subclass that
 * supplies the model, so it is never bound in the container by itself.
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function paginate(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->orderBy($this->model->getKeyName())
            ->paginate($perPage);
    }

    public function store(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function find(int $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function update(array $data, int $id): Model
    {
        $item = $this->find($id);
        $item->update($data);

        return $item->refresh();
    }

    public function delete(int $id): void
    {
        $this->find($id)->delete();
    }
}
