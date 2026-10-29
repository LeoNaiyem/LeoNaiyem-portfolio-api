<?php

use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::middleware('cache.headers:public;max_age=300;etag')->group(function () {
        Route::get('settings', [ContentController::class, 'settings'])->name('settings');
        Route::get('abouts', [ContentController::class, 'abouts'])->name('abouts');
        Route::get('tags', [ContentController::class, 'tags'])->name('tags');
        Route::get('skills', [ContentController::class, 'skills'])->name('skills');
        Route::get('experiences', [ContentController::class, 'experiences'])->name('experiences');
        Route::get('testimonials', [ContentController::class, 'testimonials'])->name('testimonials');
        Route::get('brands', [ContentController::class, 'brands'])->name('brands');
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
    });

    Route::post('contact', [ContactController::class, 'store'])
        ->middleware('throttle:contact')
        ->name('contact.store');
});
