<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => '/v1', 'middleware' => ['throttle:4,1']],function () {
    Route::post('/upload-full-csv', [EmployeeController::class, 'uploadBulkEmployeesData'])->name("upload-full-csv");
    Route::post('/upload-updated-csv', [EmployeeController::class, 'uploadUpdatedEmployeeData'])->name("upload-updated-csv");
});
