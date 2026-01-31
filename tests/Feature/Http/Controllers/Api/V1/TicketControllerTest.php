<?php

use App\Models\User;
use App\Permissions\V1\Abilities;

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

it('stores a ticket', function() {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect(Gate::forUser($user)->allows(Abilities::CreateTicket))
        ->toBeTrue();

//    Gate::shouldReceive('authorize')
//        ->once()
//        ->withArgs(function(string $store, User $user) {
//            expect($store)->toBeString('store')
//                   ->and($user)->toBeClass(User::class);
//            return true;
//        })
//        ->andReturn(Response::class);
    ;

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
                        "id" => 11
                    ]
                ]
            ]
        ]
    ]);

    $response->assertJsonStructure([
        'data'
    ]);
});
