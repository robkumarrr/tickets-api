<?php

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;

it('returns all tickets as ticket resources', function() {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->getJson(route('tickets.index'));
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data',
            'meta',
            'links'
        ]);
});

it('returns 401 is user is unauthenticated', function() {
    $response = $this->getJson(route('tickets.index'));
    $response->assertStatus(401);
});

it('stores a ticket if a user is authorized', function() {
    $user = User::factory()->create();
    Sanctum::actingAs(
        $user,
        ['ticket:create']
    );

    Gate::shouldReceive('authorize')
        ->once()
        ->with('create', Mockery::type(User::class))
        ->andReturn(true);

    $response = $this->postJson(route('tickets.index'), [
        "data"=> [
            "attributes"=> [
                "title"=> "Second Ticket",
                "description"=> "This is the second ticket we created",
                "status"=> "C"
            ],
            "relationships"=> [
                "author"=> [
                    "data"=> [
                        "id" => $user->id
                    ]
                ]
            ]
        ]
    ]);

    $response->assertStatus(Response::HTTP_CREATED);

    $response->assertJsonStructure([
        'data'
    ]);
});

it('unauthorized user cannot create ticket for another user', function() {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Sanctum::actingAs($otherUser, ['ticket:create']);

    $response = $this->postJson(route('tickets.store'), [
        "data"=> [
            "attributes"=> [
                "title"=> "Second Ticket",
                "description"=> "This is the second ticket we created",
                "status"=> "C"
            ],
            "relationships"=> [
                "author"=> [
                    "data"=> [
                        "id" => $user->id
                    ]
                ]
            ]
        ]
    ]);

    $response->assertStatus(403);
});
