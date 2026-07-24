<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Domain\Auth\Dictionaries\PermissionDictionary;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteHealthTest extends TestCase
{
    public function test_named_routes_are_unique(): void
    {
        $names = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $this->assertSame($names->count(), $names->unique()->count());
    }

    public function test_controller_routes_target_existing_methods(): void
    {
        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();

            if (! str_contains($action, '@') || str_starts_with($action, 'Closure')) {
                continue;
            }

            [$controller, $method] = explode('@', $action, 2);

            $this->assertTrue(
                class_exists($controller) && method_exists($controller, $method),
                sprintf('Route [%s] targets missing controller method [%s].', $route->getName() ?? $route->uri(), $action)
            );
        }
    }

    public function test_static_blade_route_references_are_registered(): void
    {
        $references = collect(File::allFiles(resource_path('views')))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
            ->flatMap(function ($file) {
                preg_match_all("/(?<!->)(?:\\broute|to_route)\\(\\s*['\"]([^'\"]+)['\"]/", $file->getContents(), $matches);

                return $matches[1];
            })
            ->unique();

        foreach ($references as $name) {
            $this->assertTrue(Route::has($name), sprintf('Blade references undefined route [%s].', $name));
        }
    }

    public function test_admin_menu_routes_are_registered(): void
    {
        $routes = [];
        $collectRoutes = function (array $items) use (&$collectRoutes, &$routes): void {
            foreach ($items as $item) {
                if (isset($item['route'])) {
                    $routes[] = $item['route'];
                }

                if (isset($item['children'])) {
                    $collectRoutes($item['children']);
                }
            }
        };

        $collectRoutes(config('admin-menu.menu'));

        foreach (array_unique($routes) as $name) {
            $this->assertTrue(Route::has($name), sprintf('Admin menu references undefined route [%s].', $name));
        }
    }

    public function test_admission_document_repository_binding_honors_its_contract(): void
    {
        $this->assertInstanceOf(DocumentRepositoryInterface::class, app(DocumentRepositoryInterface::class));
    }

    public function test_static_blade_includes_and_extends_target_existing_views(): void
    {
        $references = collect(File::allFiles(resource_path('views')))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
            ->flatMap(function ($file) {
                preg_match_all("/@(?:include|extends)\\(\\s*['\"]([^'\"]+)['\"]/", $file->getContents(), $matches);

                return $matches[1];
            })
            ->unique();

        foreach ($references as $view) {
            $this->assertTrue(view()->exists($view), sprintf('Blade references missing view [%s].', $view));
        }
    }

    public function test_static_blade_components_target_existing_anonymous_components(): void
    {
        $components = collect(File::allFiles(resource_path('views')))
            ->filter(fn ($file) => str_ends_with($file->getFilename(), '.blade.php'))
            ->flatMap(function ($file) {
                preg_match_all('/<x-([\w.-]+)/', $file->getContents(), $matches);

                return $matches[1];
            })
            ->reject(fn ($name) => in_array($name, ['slot', 'dynamic-component'], true))
            ->unique();

        foreach ($components as $component) {
            $path = resource_path('views/components/'.str_replace('.', '/', $component).'.blade.php');

            $this->assertFileExists($path, sprintf('Blade references missing anonymous component [%s].', $component));
        }
    }

    public function test_admin_menu_permissions_exist_in_the_dictionary(): void
    {
        $permissions = [];
        $collectPermissions = function (array $items) use (&$collectPermissions, &$permissions): void {
            foreach ($items as $item) {
                if (isset($item['permission'])) {
                    $permissions[] = $item['permission'];
                }

                if (isset($item['children'])) {
                    $collectPermissions($item['children']);
                }
            }
        };

        $collectPermissions(config('admin-menu.menu'));

        foreach (array_unique($permissions) as $permission) {
            $this->assertContains($permission, PermissionDictionary::all());
        }
    }

    public function test_critical_frontend_routes_render_successfully(): void
    {
        foreach ([
            'frontend.home' => [],
            'frontend.courses.index' => [],
            'frontend.courses.show' => ['sample-course'],
            'frontend.blogs.index' => [],
            'frontend.blogs.show' => ['sample-post'],
            'frontend.pre-register' => [],
            'frontend.contact' => [],
            'frontend.legal.kvkk' => [],
            'frontend.legal.gizlilik' => [],
        ] as $name => $parameters) {
            $this->get(route($name, $parameters))->assertOk();
        }
    }

    public function test_protected_portal_routes_redirect_guests_to_login(): void
    {
        foreach ([
            'admin.dashboard',
            'admin.admission.dashboard',
            'admin.documents.dashboard',
            'admin.students.index',
            'admin.notifications.index',
            'admin.notifications.dashboard',
            'admin.system.jobs.dashboard',
            'admin.inventory.dashboard',
            'admin.invoices.dashboard',
            'admin.crm.dashboard',
            'admin.hr.dashboard',
            'teacher.dashboard',
            'parent.dashboard',
        ] as $name) {
            $this->get(route($name))->assertRedirect(route('login'));
        }
    }

    public function test_portal_routes_require_their_role_or_administrator_role(): void
    {
        foreach ([
            'teacher.dashboard' => 'role:Teacher|Administrator',
            'parent.dashboard' => 'role:Parent|Administrator',
        ] as $name => $middleware) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertContains($middleware, $route->middleware());
        }
    }
}
