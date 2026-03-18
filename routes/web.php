<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->success(
    Response::HTTP_OK,
    'API is running'
));
