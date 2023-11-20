<?php

use App\Models\Forum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpFoundation\Response;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});


todo('Can Add comment');
todo('Can  Edit comment');
todo('Can Delete comment');
