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

Route::resource('/test', 'TestController');
// Route::resource('/test', 'App\Http\Controllers\TestController');



// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test_mode', function () {
    return 'Test Mode Activated..!';
});



Route::get('/', 'PagesController@index');
Route::get('/code80', 'Code80Controller@code80');
Route::get('/expenses', 'PagesController@expenses');
Route::get('/reports', 'PagesController@reports');
Route::get('/studentsrecycler', 'PagesController@studentsrecycler');

Route::get('/download', function () {
    return response()->download('maindir/exfile/student_file_format.xlsx');
});

Route::get('/try', 'PagesController@try');
Route::resource('/fees', 'FeesController');








Route::get('/runs', 'DashController@runs');
Route::get('/changedate', 'DashController@changedate');
Route::get('/deliverer', 'DashController@deliverer');
Route::get('/dashboard', 'DashController@dashboard');
Route::get('/config', 'DashController@configurations');
Route::get('/dashuser', 'DashController@dashuser');
Route::resource('/items', 'ItemsController');
Route::resource('/reporting', 'ReportsController');
Route::resource('/distribution', 'DistributionController');
Route::resource('/stock', 'StockController');
Route::resource('/closure', 'ClosureController');
Route::get('/reportprinting', 'DashController@reportprinting');
Route::get('/stockfillprint', 'DashController@stockfillprint');
Route::get('/stockreportprinting', 'DashController@stockreportprinting');
Route::get('/expensereportprinting', 'DashController@expensereportprinting');
Route::get('/returnprint', 'DashController@returnprint');
Route::get('/waybillprint', 'DashController@waybillprint');
Route::get('/distreportprint', 'DashController@distreportprint');

Route::get('/debtsreportprinting', function () {
    return view('pages.invoice.debtsinvoice');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/orders', 'DashController@orders');
Route::get('/waybill', 'DashController@waybill');
Route::get('/waybillview', 'DashController@waybillview');
Route::get('/sales', 'DashController@sales');
Route::get('/mpt_cart', 'DashController@empty_cart');
Route::get('/stockbal', 'DashController@stockbal');
Route::get('/genstockbal', 'DashController@genstockbal');
Route::get('/expensereport', 'DashController@expensereport');
Route::get('/debts', 'DashController@debts');
Route::get('/waybillreport', 'DashController@waybillreport');
Route::get('/returnsreport', 'DashController@returnsreport');
Route::get('/distreport', 'DashController@distreport');
Route::get('/closure_page', 'DashController@closure');
Route::get('/saleshistory', 'DashController@saleshistory');

// Route::get('/reporting', 'DashController@reporting');
