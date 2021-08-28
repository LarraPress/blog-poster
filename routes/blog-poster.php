<?php

use Illuminate\Support\Facades\Route;
use LarraPress\BlogPoster\Http\Controllers\Dashboard\HomeController;
use LarraPress\BlogPoster\Http\Controllers\Dashboard\JobsController;
use LarraPress\BlogPoster\Http\Controllers\Dashboard\LogsController;

Route::group(['prefix' => 'blog-poster', 'as' => 'blog-poster.'], function () {
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('logs', [LogsController::class, 'dashboard'])->name('logs');

    Route::group(['prefix' => 'jobs', 'as' => 'jobs.'], function () {
        Route::get('/', [JobsController::class, 'create'])->name('create');
        Route::post('/', [JobsController::class, 'store'])->name('store');

        Route::get('/source_icon', [JobsController::class, 'parseSourceIcon'])->name('source_icon');
        Route::post('/test', [JobsController::class, 'test'])->name('test');

        Route::get('/{id}', [JobsController::class, 'edit'])->name('edit');
        Route::get('/copy/{id}', [JobsController::class, 'copy'])->name('copy');
        Route::post('/{id}', [JobsController::class, 'update'])->name('update');

        Route::delete('/{id}', [JobsController::class, 'delete'])->name('delete');
    });
});
