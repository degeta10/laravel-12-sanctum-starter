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
    /**
     * Check if exception is a throttle request exception
     */
    public function isThrottle(Throwable $e): bool
    {
        return $e instanceof ThrottleRequestsException;
    }

    /**
     * Check if exception is an authentication exception
     */
    public function isAuthentication(Throwable $e): bool
    {
        return $e instanceof AuthenticationException;
    }

    /**
     * Check if exception is a model not found exception
     */
    public function isModel(Throwable $e): bool
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     * Check if exception is an HTTP not found exception
     */
    public function isHttp(Throwable $e): bool
    {
        return $e instanceof NotFoundHttpException;
    }

    /**
     * Check if exception is a validation exception
     */
    public function isValidationException(Throwable $e): bool
    {
        return $e instanceof ValidationException;
    }

    /**
     * Handle throttle exception
     */
    public function throttleResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_TOO_MANY_REQUESTS,
            'Too many attempts, please try after a minute.',
        );
    }

    /**
     * Handle authentication exception
     */
    public function authResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_UNAUTHORIZED,
            'Unauthenticated',
        );
    }

    /**
     * Handle model not found exception
     */
    public function modelResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_NOT_FOUND,
            'Not found',
        );
    }

    /**
     * Handle HTTP not found exception
     */
    public function httpResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_NOT_FOUND,
            'Incorrect route',
        );
    }

    /**
     * Handle general exception
     */
    public function internalResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            'Internal server error',
        );
    }

    /**
     * Handle validation exception
     */
    public function validationResponse(Throwable $e)
    {
        return response()->error(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Validation failed',
            $e->errors()
        );
    }

    /**
     * Render the exception response
     */
    public function render($request, Throwable $e)
    {
        if ($this->isThrottle($e)) {
            return $this->throttleResponse($e);
        } elseif ($this->isAuthentication($e)) {
            return $this->authResponse($e);
        } elseif ($this->isModel($e)) {
            return $this->modelResponse($e);
        } elseif ($this->isHttp($e)) {
            return $this->httpResponse($e);
        } elseif ($this->isValidationException($e)) {
            return $this->validationResponse($e);
        }

        return $this->internalResponse($e);
    }
}
