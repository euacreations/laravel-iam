<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class FeatureSyncer
{
    /**
     * Sync features from routes into storage.
     */
    public static function sync(Command $command, bool $dryRun = false): void
    {
        $discovered = self::discoverFeatureSlugs();

        if ($discovered === []) {
            $command->info('No features found in routes.');
            return;
        }

        $command->info('Discovered '.count($discovered).' feature(s).');

        if ($dryRun) {
            foreach ($discovered as $slug) {
                $command->line(" - {$slug}");
            }
            return;
        }

        foreach ($discovered as $slug) {
            $feature = Feature::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => Str::headline(str_replace('.', ' ', $slug)),
                    'guard_name' => 'web',
                    'is_builtin' => false,
                ]
            );

            self::autoAssignFeature($feature);
        }

        $command->info('Feature sync complete.');
    }

    /**
     * @return string[]
     */
    protected static function discoverFeatureSlugs(): array
    {
        $slugs = [];

        foreach (Route::getRoutes() as $route) {
            $routeName = $route->getName();

            foreach ($route->gatherMiddleware() as $middleware) {
                if (! is_string($middleware)) {
                    continue;
                }

                if (! str_starts_with($middleware, 'feature:')) {
                    continue;
                }

                $params = substr($middleware, strlen('feature:'));
                if ($params === '') {
                    continue;
                }

                foreach (explode(',', $params) as $slug) {
                    $slug = trim($slug);
                    if ($slug !== '') {
                        $slugs[$slug] = true;
                    }
                }
            }

            if ($routeName) {
                $slugs[$routeName] = true;
            }
        }

        return array_keys($slugs);
    }

    protected static function autoAssignFeature(Feature $feature): void
    {
        $roles = Role::where('auto_assign_new_features', true)->get();
        foreach ($roles as $role) {
            $role->features()->syncWithoutDetaching($feature->id);
        }

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->features()->syncWithoutDetaching($feature->id);
        }
    }
}
