<?php

namespace App\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Standardized API response builder.
 *
 * Response envelope:
 * - status: success | error | fail
 * - message: string
 * - data: mixed|null
 * - meta: array|null
 * - errors: mixed|null
 */
class ApiResponse
{
    /**
     * Build a successful API response.
     *
     * @param mixed $data
     * @param string $message
     * @param array<string, mixed>|null $meta
     * @param int $code
     * @return JsonResponse
     */
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        ?array $meta = null,
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'errors' => null,
        ], $code);
    }

    /**
     * Build an error API response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Error',
        mixed $errors = null,
        int $code = 500
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Build a failed API response (typically validation/business rule failures).
     *
     * @param string $message
     * @param mixed $errors
     * @param int $code
     * @return JsonResponse
     */
    public static function fail(
        string $message = 'Validation failed',
        mixed $errors = null,
        int $code = 422
    ): JsonResponse {
        return response()->json([
            'status' => 'fail',
            'message' => $message,
            'data' => null,
            'meta' => null,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Extract strict pagination metadata for API responses.
     *
     * Allowed keys only:
     * - current_page
     * - per_page
     * - total
     * - last_page
     * - from
     * - to
     * - has_more
     *
     * @return array{
     *     current_page:int,
     *     per_page:int,
     *     total:int,
     *     last_page:int,
     *     from:int|null,
     *     to:int|null,
     *     has_more:bool
     * }
     */
    public static function paginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more' => $paginator->hasMorePages(),
        ];
    }
}
