<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

/**
 * Xarxa de seguretat: comprova que cap ruta registrada apunti a un controlador
 * o mètode que no existeix. Abans hi havia 31 rutes (resources d'admin i
 * motorcycles.show) que donaven un 500 en obrir-les.
 */
class RouteIntegrityTest extends TestCase
{
    public function test_every_route_points_to_an_existing_controller_action(): void
    {
        $broken = [];

        foreach (RouteFacade::getRoutes() as $route) {
            $action = $route->getAction('uses');

            if (! is_string($action) || ! str_contains($action, '@')) {
                continue; // Closures i rutes de vista: no hi ha res a comprovar.
            }

            [$class, $method] = explode('@', $action, 2);

            if (! class_exists($class)) {
                $broken[] = sprintf('%s -> classe inexistent %s', $route->uri(), $class);
                continue;
            }

            if (! method_exists($class, $method)) {
                $broken[] = sprintf('%s -> %s::%s() no existeix', $route->uri(), $class, $method);
            }
        }

        $this->assertSame([], $broken, "Rutes trencades:\n" . implode("\n", $broken));
    }

    public function test_route_names_are_unique(): void
    {
        $names = [];
        foreach (RouteFacade::getRoutes() as $route) {
            if ($name = $route->getName()) {
                $names[] = $name;
            }
        }

        $duplicates = array_keys(array_filter(array_count_values($names), fn ($count) => $count > 1));

        $this->assertSame([], $duplicates, 'Noms de ruta duplicats: ' . implode(', ', $duplicates));
    }
}
