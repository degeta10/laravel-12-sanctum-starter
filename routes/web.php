<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

Route::get('/', function () {
    return response()->success(
        Response::HTTP_OK,
        "API is running"
    );
});
