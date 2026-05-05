<?php

namespace Tests\Feature;

use Tests\TestCase;

class SmokeTest extends TestCase
{
    public function test_healthz_returns_ok(): void
    {
        $response = $this->get('/healthz');

        $response->assertStatus(200);
        $this->assertSame('ok', $response->getContent());
    }

    public function test_login_screen_renders(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider protectedRoutesProvider
     */
    public function test_protected_routes_redirect_to_login_when_unauthenticated(string $path): void
    {
        $response = $this->get($path);

        $response->assertRedirect('/login');
    }

    public static function protectedRoutesProvider(): array
    {
        return [
            'dashboard'    => ['/dashboard'],
            'inventario'   => ['/inventario'],
            'pedidos'      => ['/pedidos'],
            'ventas'       => ['/ventas'],
            'detalles'     => ['/detalles'],
            'proveedores'  => ['/proveedores'],
            'entidades'    => ['/entidades'],
            'profile'      => ['/profile'],
        ];
    }
}
