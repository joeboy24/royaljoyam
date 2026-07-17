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
Route::get('/expenses', 'ExpensesController@index')->name('expenses.index');
Route::post('/expenses', 'ExpensesController@store')->name('expenses.store');
Route::delete('/expenses/{expense}', 'ExpensesController@destroy')->name('expenses.destroy');
Route::redirect('/reports', '/reporting');

Route::get('/user_profile', 'ProfileController@edit')->name('user_profile');
Route::put('/user_profile', 'ProfileController@update')->name('user_profile.update');
Route::get('/try', 'PagesController@try');








Route::redirect('/itemsview', '/items');
Route::redirect('/stockview', '/items');

Route::get('/runs', 'DashController@runs');
Route::get('/changedate', 'DashController@changedate');
Route::get('/deliverer', 'DashController@deliverer');
Route::get('/dashboard', 'DashController@dashboard');
Route::get('/config', 'DashController@configurations');
Route::get('/dashuser', 'DashController@dashuser');
Route::get('/items/export', 'ItemsController@exportInventory');
Route::get('/items/print', 'ItemsController@printInventory');
Route::post('/items/{id}/transfer', 'ItemsController@transferStock');
Route::resource('/items', 'ItemsController');
Route::resource('/reporting', 'ReportsController');
Route::resource('/distribution', 'DistributionController');
Route::resource('/stock', 'StockController');
Route::post('/closure/{month}/open', 'ClosureController@open')->name('closure.open');
Route::post('/closure/{month}/close', 'ClosureController@close')->name('closure.close');
Route::resource('/closure', 'ClosureController');
Route::get('/reportprinting', 'DashController@reportprinting');
Route::get('/stockfillprint', 'DashController@stockfillprint');
Route::get('/stockreportprinting', 'DashController@stockreportprinting');
Route::get('/expensereportprinting', 'DashController@expensereportprinting');
Route::get('/returnprint', 'DashController@returnprint');
Route::get('/waybillprint/{id}', 'WaybillReportController@waybillPrintSingle');
Route::get('/waybillprint', 'WaybillReportController@waybillPrint');
Route::get('/distreportprint', 'WaybillReportController@distReportPrint');

Route::get('/debtsreportprinting', 'DashController@debtsreportprinting');



Auth::routes();

Route::redirect('/home', '/dashboard')->name('home');
Route::get('/orders', 'DashController@orders');
Route::redirect('/waybil', '/waybill');
Route::get('/waybill', 'WaybillController@create');
Route::post('/waybill', 'WaybillController@store');
Route::get('/waybillview', 'WaybillController@index');
Route::put('/waybill/{waybill}', 'WaybillController@update');
Route::delete('/waybill/{waybill}', 'WaybillController@destroy');
Route::put('/waybill/{waybill}/restore', 'WaybillController@restore');
Route::post('/waybill/{waybill}/contents', 'WaybillContentController@store');
Route::put('/waybill/contents/{wbcontent}', 'WaybillContentController@update');
Route::delete('/waybill/contents/{wbcontent}', 'WaybillContentController@destroy');
Route::put('/waybill/contents/{wbcontent}/distribute', 'WaybillContentController@distribute');
Route::post('/waybill/{waybill}/distribute-all', 'WaybillContentController@distributeAll');
Route::get('/sales', 'DashController@sales');
Route::post('/sales/cart', 'SalesController@addToCart');
Route::post('/sales/checkout', 'SalesController@checkout');
Route::post('/sales/pay-debt', 'SalesController@payDebt');
Route::put('/sales/cart/{cart}', 'SalesController@updateCartQuantity');
Route::delete('/sales/cart/{cart}', 'SalesController@removeCartItem');
Route::put('/sales/{sale}', 'SalesController@updateSale');
Route::put('/sales/history/{salesHistory}/deliver', 'SalesController@deliverLineItem');
Route::put('/sales/history/{salesHistory}/undeliver', 'SalesController@undeliverLineItem');
Route::delete('/sales/payments/{salesPayment}', 'SalesController@deletePaidDebtPayment');
Route::get('/mpt_cart', 'DashController@empty_cart');
Route::get('/stockbal', 'DashController@stockbal');
Route::get('/branchtransfers', 'DashController@branchTransfersReport');
Route::get('/genstockbal', 'DashController@genstockbal');
Route::get('/expensereport', 'DashController@expensereport');
Route::get('/debts', 'DashController@debts');
Route::get('/paid_debts', 'DashController@debts_paid');
Route::get('/waybillreport/export', 'WaybillReportController@exportWaybillReport');
Route::get('/waybillreport', 'WaybillReportController@waybillReport');
Route::get('/returnsreport', 'DashController@returnsreport');
Route::get('/distreport/export', 'WaybillReportController@exportDistReport');
Route::get('/distreport', 'WaybillReportController@distReport');
Route::get('/closure_page', 'DashController@closure');
Route::get('/saleshistory', 'DashController@saleshistory');

// Route::get('/reporting', 'DashController@reporting');
