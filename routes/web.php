<?php

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
Route::get('/', 'DashboardController@show')->middleware('auth');

//Login and registration forms
Auth::routes();
Route::get('/login/{role}', 'Auth\LoginController@showLoginForm');
Route::get('/register/{role}', 'Auth\RegisterController@showRegistrationForm');
Route::post('/register/{role}', 'Auth\RegisterController@register');

//Authors pages
Route::get('/author/{id}', 'AuthorsController@index')->name('authors.index');
Route::post('/author/save', 'AuthorsController@save')->name('authors.save')->middleware('auth');
Route::get('/authors/', 'AuthorsController@list')->name('authors.list');

//Book pages
Route::get('/book/new', 'BooksController@create')->middleware('auth');
Route::post('/book/save', 'BooksController@save')->middleware('auth');
Route::post('/book/update-status', 'BooksController@updateStatus')->middleware('auth');
Route::post('/book/saveContract', 'BooksController@saveContract')->middleware('auth');
Route::get('/book', 'BooksController@all')->name('books.active-crowdsale');
Route::post('/book/buy', 'BooksController@buy')->middleware('auth');
Route::post('/book/upload', 'BooksController@upload')->middleware('auth');
Route::post('/book/upload-cover', 'BooksController@uploadCover')->middleware('auth');
Route::get('/book/download/{id}', 'BooksController@download')->middleware('auth');
Route::get('/book/read/{id}', 'BooksController@read')->middleware('auth');
Route::get('/book/download_url/{id}', 'BooksController@getDownloadUrl')->middleware('auth');
Route::get('/book/edit/{id}', 'BooksController@showEdit')->middleware('auth');

//kyc pages
Route::get('/kyc', 'KycController@index')->middleware('auth');
Route::post('/kyc', 'KycController@save')->middleware('auth');

Route::resource('/user', 'UserController');

//User
Route::post('/wallet/create', 'UserController@createWallet')->name('user.create_wallet')->middleware('auth');

//pretty book urls (this must be last entry since it catches everything)
Route::get('/{url}', 'BooksController@show');
