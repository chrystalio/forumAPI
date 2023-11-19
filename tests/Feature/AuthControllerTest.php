<?php


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

test('User can register', function () {
    $user = User::factory()->make();

    $response = $this->postJson('/api/register', $user->toArray() + [
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'success',
            'user' => [
                'username',
                'email',
                'updated_at',
                'created_at',
                'id'
            ]
        ]);

    $this->assertDatabaseHas('users', [
        'username' => $user->username,
        'email' => $user->email
    ]);
});

test('User can login', function () {
    $user = User::factory()->create();

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk()
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
});

test('Authenticated user can check profile', function () {
    $user = User::factory()->create();

    // Login to obtain the initial token
    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    $token = $loginResponse->json('access_token');

    $this->postJson('/api/auth/me', [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertOk()
        ->assertJsonStructure([
            'id',
            'username',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
        ]);
});

test('Authenticated user can refresh token', function () {
    $user = User::factory()->create();

    // Login to obtain the initial token
    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    $token = $loginResponse->json('access_token');

    // Refresh the token
    $this->postJson('/api/auth/refresh', [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertOk()
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
});

test('Authenticated user can log out', function () {
    $user = User::factory()->create();

    // Login to obtain the initial token
    $loginResponse = $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertOk();

    $token = $loginResponse->json('access_token');

    // Logout
    $this->postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertOk();

    // Attempt to refresh the token and assert unauthorized
    $this->postJson('/api/auth/refresh', [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertUnauthorized();

    // Attempt to logout again and assert unauthorized
    $this->postJson('/api/auth/logout', [], [
        'Authorization' => 'Bearer ' . $token,
    ])->assertUnauthorized();
});
