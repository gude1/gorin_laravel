<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/get-report", [ReportController::class, "getReport"]);
Route::get("/get-item-list", [ItemController::class, 'getItems']);