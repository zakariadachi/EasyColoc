<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class BanMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_is_logged_out_and_redirected(): void
    {
        $user = User::create([
            'name' => 'Banned User',
            'email' => 'banned@example.com',
            'password' => bcrypt('password'),
            'is_banned' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/profile');

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Your account has been banned.');
        $this->assertFalse(Auth::check());
    }

    public function test_active_user_can_access_protected_routes(): void
    {
        $user = User::create([
            'name' => 'Active User',
            'email' => 'active@example.com',
            'password' => bcrypt('password'),
            'is_banned' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/profile');

        $response->assertStatus(200);
        $this->assertTrue(Auth::check());
    }
}
