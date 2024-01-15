<?php

namespace App\Http\Controllers;

use App\Interfaces\BaseRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shared read/delete behaviour for the resourceful controllers.
 *
 * `store` and `update` are deliberately *not* here: each resource validates
 * through its own FormRequest, and PHP does not allow a subclass to narrow an
 * inherited `Request` parameter. Keeping them per-controller stays explicit.
 */
abstract class BaseController extends Controller
{
    /** Upper bound so `?per_page=1000000` cannot be used to pull a whole table. */
    private const MAX_PER_PAGE = 100;

    private const DEFAULT_PER_PAGE = 15;

    public function __construct(protected BaseRepositoryInterface $repository)
    {
    }

    /** The resource class used to serialise this controller's model. */
    abstract protected function resourceClass(): string;

    public function index(Request $request): JsonResponse
    {
        $page = $this->repository->paginate($this->perPage($request));

        return $this->resourceClass()::collection($page)->response();
    }

    public function show(int $id): JsonResponse
    {
        return $this->resource($this->repository->find($id))->response();
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repository->delete($id);

        return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    protected function resource(mixed $model): JsonResource
    {
        return $this->resourceClass()::make($model);
    }

    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', (string) self::DEFAULT_PER_PAGE);

        return max(1, min($perPage ?: self::DEFAULT_PER_PAGE, self::MAX_PER_PAGE));
    }
}
