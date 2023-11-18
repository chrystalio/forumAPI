<?php


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

//test('User can register', function (){
//    $user = User::factory()->make();
//
//    $response = $this->postJson('/api/register', [
//        'username' => $user->username,
//        'email' => $user->email,
//        'password' => 'password',
//        'password_confirmation' => 'password'
//    ]);
//
//    $response->assertStatus(201);
//    $response->assertJsonStructure([
//        'success',
//        'user' => [
//            'username',
//            'email',
//            'updated_at',
//            'created_at',
//            'id'
//        ]
//    ]);
//
//    $this->assertDatabaseHas('users', [
//        'username' => $user->username,
//        'email' => $user->email
//    ]);
//});

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
