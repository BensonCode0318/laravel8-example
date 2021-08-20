<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Resources\Traits\BaseResourceTrait;
use Illuminate\Pagination\AbstractPaginator;

/**
 * BaseResourceCollection
 */
class BaseResourceCollection extends ResourceCollection
{
    use BaseResourceTrait;

    /**
     * Create an HTTP response that represents the object.
     *
     * @param mixed $request Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginatedResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }
}
