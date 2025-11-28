<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('inventories.index'));
        $this->assertAuthenticated();

        $this->post(route('logout'));
        $this->assertGuest();

        $login = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $login->assertRedirect(route('inventories.index'));
        $this->assertAuthenticated();
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword!'),
        ]);

        $this->actingAs($user)
            ->put(route('password.change.update'), [
                'current_password' => 'OldPassword!',
                'password' => 'NewPassword!1',
                'password_confirmation' => 'NewPassword!1',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('NewPassword!1', $user->refresh()->password));
    }
}





