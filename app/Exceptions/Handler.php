<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler
{
    protected array $map = [
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
    ];

    public function render(Throwable $e)
    {
        foreach ($this->map as $class => $config) {
            if ($e instanceof $class) {
                return response()->error(
                    $config['status'],
                    $config['message']
                );
            }
        }

        if ($e instanceof ValidationException) {
            return response()->error(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                'Validation failed',
                $e->errors()
            );
        }

        return response()->error(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            app()->isProduction()
                ? 'Internal server error'
                : $e->getMessage()
        );
    }
}
