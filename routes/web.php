<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->success(
        Response::HTTP_OK,
        'API is running'
    );
});
