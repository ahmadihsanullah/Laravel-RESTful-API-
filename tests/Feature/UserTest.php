<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post('/api/users', [
            'username' => "ahmad",
            'name' => "ahmad",
            'password' => "rahasia"
        ])->assertStatus(201)
            ->assertJson([
               "data" => [
                 "username" => "ahmad",
                "name" =>"ahmad"
               ]
            ]);
    }

    public function testRegisterFailed() {
        $this->post('/api/users', [
            'username' => "",
            'name' => "",
            'password' => ""
        ])->assertStatus(400)
            ->assertJson([
               "errors" => [
                 "username" => [
                        "The username field is required."
                 ],
                "password" =>[
                    "The password field is required."
                ]
               ]
            ]);
    }

    public function testRegisterUsernameAlreadyExist() {
        $this->testRegisterSuccess();
        $this->post('/api/users', [
            'username' => "ahmad",
            'name' => "ahmad",
            'password' => "rahasia"
        ])->assertStatus(400)
            ->assertJson([
               "errors" => [
                    "username" => ["username already registered"]
                ]
            ]);
    }
}
