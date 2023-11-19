<?php

use App\Models\Forum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('can get a paginated list of forums', function () {
    $forum = Forum::factory()->create();

    $this->get('/api/forums')
        ->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'body',
                    'category',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'user' => [
                        'id',
                        'username',
                    ],
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => [
                    'url',
                    'label',
                    'active',
                ],
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

    $this->assertDatabaseHas('forums', [
        'title' => $forum->title,
        'slug' => $forum->slug,
        'body' => $forum->body,
        'category' => $forum->category,
        'user_id' => $forum->user_id,
    ]);
});


test('can get a specific post forum', function () {
    $forum = Forum::factory()->create();

    $this->get("/api/forums/{$forum->id}")
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'body',
                'slug',
                'category',
                'created_at',
                'updated_at',
                'user' => [
                    'id',
                    'username',
                ],
                'comments',
            ],
        ]);

    $this->assertDatabaseHas('forums', $forum->only([
            'title',
            'slug',
            'body',
            'category',
            'user_id'
        ]
    ));
});

