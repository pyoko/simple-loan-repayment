<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function() {
	Route::post('register', 'Auth\RegisterController@register')->name('register');

	/**
	 * We just create a simple API here, so let's just put aside any authentication
	 * for the API. So we don't need login/logout (¯\_(ツ)_/¯)
	 *
	 * But in case, we do need authentication. We could set-up Passport :)
	 *
	 * The end points, are quite terrible I would say 
	 * since we're not using the authentication for the user.
	 *
	 * For example, user can delete other user's loan or repayments
	 * if s/he knows the api end points
	 *
	 * Therefore, it would be much better (obviously) to use authentication
	 * the authenticated user with admin role has the privileges to list all users,
	 * loans, repayments
	 *
	 * the authenticated user with user role has the privileges to list (limited to) his/her user, 
	 * loan, repayments detail
	 *
	 */
	//Route::post('login', 'Auth\LoginController@login');
	//Route::post('logout', 'Auth\LoginController@logout');

	//Route::middleware('auth:api')->group(function() {
		# User end points
		Route::apiResource('users', 'API\UserController')->only([
			'index', 'show', 'update', 'destroy'
		]);

		# Loan end points
		Route::get('loans', 					'API\LoanController@index')->name('loans.index');
		Route::get('loans/{loan}', 				'API\LoanController@show')->name('loans.show');
		Route::get('users/{user}/loans', 		'API\LoanController@showByUser')->name('loans.showByUser');
		Route::post('users/{user}/loans', 		'API\LoanController@store')->name('loans.store');
		Route::delete('loans/{loan}', 			'API\LoanController@destroy')->name('loans.destroy');

		# Repayment end points
		Route::get('repayments', 				'API\RepaymentController@index')->name('repayments.index');
		Route::get('repayments/{repayment}', 	'API\RepaymentController@show')->name('repayments.show');
		Route::post('loans/{loan}/repayments', 	'API\RepaymentController@store')->name('repayments.store');
		Route::delete('repayments/{repayment}', 'API\RepaymentController@destroy')->name('repayments.destroy');
	//});
});