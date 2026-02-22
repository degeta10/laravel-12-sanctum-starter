<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        /**
         * Success response macro
         * 
         * @param mixed $data - Response data
         * @param string $message - Response message
         * @param int $code - HTTP status code
         */
        Response::macro('success', function (
            $code = HttpResponse::HTTP_OK,
            $message = 'Success',
            $data = null
        ) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $code);
        });


        /**
         * Error response macro
         * 
         * @param string $message - Error message
         * @param array $errors - Validation errors or additional error details
         * @param int $code - HTTP status code
         */
        Response::macro('error', function (
            int $code = HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
            string $message = 'Error',
            array $errors = []
        ) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $code);
        });
    }
}
