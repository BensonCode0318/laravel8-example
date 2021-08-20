<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Facades\Log;

/**
 * PaginatedResourceResponse class
 * 調整 page response 參數
 */
class PaginatedResourceResponse extends ResourceResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param mixed $request Request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        return tap(response()->json(
            $this->wrap(
                ["data" => $this->resource->resolve($request)],
                array_merge_recursive(
                    $this->paginationInformation($request),
                    $this->resource->with($request),
                    $this->resource->additional
                )
            ),
            $this->calculateStatus()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource->pluck('resource');
            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Add the pagination information to the response.
     *
     * @param mixed $request Request
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function paginationInformation($request)
    {
        $paginated = $this->resource->resource->toArray();

        return [
            'response' => $this->page($paginated),
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param array $paginated Paginated
     *
     * @return array
     */
    protected function page(array $paginated)
    {
        return [
            "page"       => (int) $paginated["current_page"],
            "total"      => isset($paginated["total"]) ? $paginated["total"] : 0,
            "total_page" => isset($paginated["last_page"]) ? $paginated["last_page"] : 1,
            "limit"      => (int) $paginated["per_page"]
        ];
    }
}
