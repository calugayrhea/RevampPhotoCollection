<?php

namespace App\Support;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use PrinsFrank\Standards\Http\HttpStatusCode;

class ApiEcho
{
    /**
     * Generate a JSON response.
     *
     * @param  int|HttpStatusCode  $status HTTP status code
     * @param  mixed|null  $message Response message
     * @param  mixed|null  $data Response data
     */
    public static function response(mixed $status, $message = null, $data = null): JsonResponse
    {
        $code = $status;

        if ($status instanceof HttpStatusCode) {
            $code = $status->value;
        }

        $message = $message ?? self::getHttpCodeMessages($code);

        $response = collect([
            'status' => $code,
            'message' => $message,
        ])
            ->when($data, function ($collection) use ($data) {
                return $collection->merge(['data' => $data]);
            })
            ->toArray();

        return response()->json($response, $code);
    }

    /**
     * Generate a JSON response for validation errors.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function responseErrorValidation(Validator $validator): JsonResponse
    {
        $e = new ValidationException($validator);

        $response = [
            'status' => $e->status,
            'message' => $e->getMessage(),
            'errors' => $e->errors(),
        ];

        return response()->json($response, $e->status);
    }

    private static function getHttpCodeMessages($code)
    {
        $message = collect(HttpStatusCode::cases())->map(fn ($item) => $item->name)->toArray();

        return $message[$code] ?? '';
    }
}
