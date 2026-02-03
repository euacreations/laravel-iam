<?php

use EuaCreations\LaravelIam\Models\Feature;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

it('prunes orphan features', function () {
    Feature::create([
        'name' => 'Old Route',
        'slug' => 'old.route',
        'guard_name' => 'web',
        'is_builtin' => false,
    ]);

    Route::get('/profile', fn () => 'ok')
        ->name('profile.view')
        ->middleware('feature');

    Artisan::call('iam:feature-prune', ['--dry-run' => true]);
    expect(Feature::where('slug', 'old.route')->exists())->toBeTrue();

    Artisan::call('iam:feature-prune');
    expect(Feature::where('slug', 'old.route')->exists())->toBeFalse();
});
