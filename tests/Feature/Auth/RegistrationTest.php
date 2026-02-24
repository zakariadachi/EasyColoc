<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_user_is_promoted_to_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'First User',
            'email' => 'first@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::first();
        $this->assertTrue($user->is_admin);
    }

    public function test_second_user_is_not_admin(): void
    {
        User::create([
            'name' => 'First User',
            'email' => 'first@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        $response = $this->post('/register', [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'second@example.com')->first();
        $this->assertFalse($user->is_admin);
    }
}
