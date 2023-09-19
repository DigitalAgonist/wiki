<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [Controller::class, 'show'])->name('home');
Route::get('/wiki', [Controller::class, 'show'])->name('home');
Route::get('/wiki/load', [Controller::class, 'load'])->name('load');
Route::get('/wiki/{pageName}', [Controller::class, 'importArticle'])->name('wikitext');
Route::get('/wiki/search/{word}', [Controller::class, 'searchArticles'])->name('search');
Route::get('/wiki/article/{id}', [Controller::class, 'getArticle'])->name('article');
