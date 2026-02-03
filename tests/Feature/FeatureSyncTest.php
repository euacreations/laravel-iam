<?php

use EuaCreations\LaravelIam\Models\Feature;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

it('syncs features from named routes and feature middleware', function () {
    Route::get('/profile/edit', fn () => 'ok')
        ->name('profile.edit')
        ->middleware('feature');

    Route::get('/admin', fn () => 'ok')
        ->middleware('feature:admin.dashboard');

    Artisan::call('iam:sync-features');

    expect(Feature::where('slug', 'profile.edit')->exists())->toBeTrue();
    expect(Feature::where('slug', 'admin.dashboard')->exists())->toBeTrue();
});
