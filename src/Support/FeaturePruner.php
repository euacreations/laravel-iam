<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Feature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class FeaturePruner
{
    /**
     * Remove features that no longer exist in routes.
     */
    public static function prune(Command $command, bool $dryRun = false): void
    {
        $slugs = self::discoverFeatureSlugs();

        $query = Feature::query();
        if ($slugs !== []) {
            $query->whereNotIn('slug', $slugs);
        }

        $orphans = $query->orderBy('id')->get(['id', 'slug', 'name']);

        if ($orphans->isEmpty()) {
            $command->info('No orphan features found.');
            return;
        }

        $command->info('Orphan features: '.count($orphans));

        if ($dryRun) {
            foreach ($orphans as $feature) {
                $command->line(" - {$feature->slug}");
            }
            return;
        }

        foreach ($orphans as $feature) {
            $feature->delete();
        }

        $command->info('Orphan features removed.');
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
}
