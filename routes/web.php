<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('Employee.employee');
});
Route::resource('employee', 'App\Http\Controllers\EmployeeController');
Route::any('employee/dt', 'App\Http\Controllers\EmployeeController@datatable')->name('employee.datatable');
Route::get('/home', function() {
    return view('home');
})->name('home');
