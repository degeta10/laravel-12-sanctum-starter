<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

final class Handler
{
    use ApiResponse;

    private array $map = [
        ThrottleRequestsException::class => [
            'status' => Response::HTTP_TOO_MANY_REQUESTS,
            'message' => 'Too many attempts, please try after a minute.',
        ],
        AuthenticationException::class => [
            'status' => Response::HTTP_UNAUTHORIZED,
            'message' => 'Unauthenticated',
        ],
        ModelNotFoundException::class => [
            'status' => Response::HTTP_NOT_FOUND,
            'message' => 'Not found',
        ],
        NotFoundHttpException::class => [
            'status' => Response::HTTP_NOT_FOUND,
            'message' => 'Incorrect route',
        ],
        InternalErrorException::class => [
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Internal server error',
        ],
    ];

    public function render(Throwable $e): \Illuminate\Http\JsonResponse
    {
        foreach ($this->map as $class => $config) {
            if ($e instanceof $class) {
                return $this->error(
                    $config['message'],
                    $config['status'],
                );
            }
        }

        if ($e instanceof ValidationException) {
            return $this->validationError(
                $e->errors()
            );
        }

        return $this->error(
            app()->isProduction()
                ? 'Internal server error'
                : $e->getMessage()
        );
    }
}
