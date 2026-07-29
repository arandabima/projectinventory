<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAreaAccessTest extends TestCase
{
    private function makeAdmin(): User
    {
        return new User(['role' => 'admin']);
    }

    private function makeUser(): User
    {
        return new User(['role' => 'user']);
    }

    public function test_user_role_middleware_allows_user_access_and_blocks_admin_area(): void
    {
        $user = $this->makeUser();
        $request = Request::create('/user/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn () => response('ok'), 'user');

        $this->assertSame('ok', $response->getContent());
        $this->assertTrue(Route::has('user.dashboard'));
        $this->assertTrue(Route::has('user.items.index'));

        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(fn () => $user);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn () => response('ok'), 'admin');
    }

    public function test_admin_role_middleware_allows_admin_access_and_blocks_user_area(): void
    {
        $admin = $this->makeAdmin();
        $request = Request::create('/admin/dashboard', 'GET');
        $request->setUserResolver(fn () => $admin);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle($request, fn () => response('ok'), 'admin');

        $this->assertSame('ok', $response->getContent());
        $this->assertTrue(Route::has('admin.dashboard'));
        $this->assertTrue(Route::has('admin.items.index'));

        $request = Request::create('/user/dashboard', 'GET');
        $request->setUserResolver(fn () => $admin);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $middleware->handle($request, fn () => response('ok'), 'user');
    }
}
