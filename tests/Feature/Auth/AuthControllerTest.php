<?php

it("checks if returning success from api/login", function () {
    $this->post('api/login')
        ->assertStatus(200)
        ->assertJsonStructure([
            'message', 'status'
        ])
        ->assertJson([
            'message' => "Hello, Login",
            'status' => 200
        ]);
});

it('checks if the api authentication is working on api/login', function() {

    $this->post('api/login')
        ->assertStatus(200)
        ->assertJsonStructure([
            'message', 'status'
        ])
        ->assertJson([
            'message' => "Hello, Login",
            'status' => 200
        ]);
});


