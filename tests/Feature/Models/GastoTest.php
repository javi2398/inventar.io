<?php

namespace Tests\Feature\Models;

use App\Models\Gasto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GastoTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_user(): void
    {
        $user  = User::factory()->create();
        $gasto = Gasto::factory()->create(['id_user' => $user->id]);

        $this->assertSame($user->id, $gasto->user->id);
    }

    public function test_can_persist_a_recurrent_gasto(): void
    {
        $gasto = Gasto::factory()->create([
            'gasto_recurrente' => true,
            'precio'           => 99.99,
        ]);

        $this->assertTrue((bool) $gasto->fresh()->gasto_recurrente);
        $this->assertEquals(99.99, $gasto->fresh()->precio);
    }
}
