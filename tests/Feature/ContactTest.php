<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use Database\Seeders\ContactCollectionSeeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    public function testCreateContactSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            "first_name" => "ahmad",
            "last_name" => "ihsan",
            "email" => "ahmad@gmail.com",
            "phone" => "08979276362",
        ], [
            'authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "ahmad",
                    "email" => "ahmad@gmail.com",
                    "phone" => "08979276362",
                ]
            ]);
    }

    public function testCreateContactFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Khannedy',
            'email' => 'eko',
            'phone' => '03242343243'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'first_name' => [
                        'The first name field is required.'
                    ],
                    'email' => [
                        'The email field must be a valid email address.'
                    ]
                ]
            ]);
    }

    public function testCreateContactUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->post('/api/contacts', [
            "first_name" => "ahmad",
            "last_name" => "ihsan",
            "email" => "ahmad@gmail.com",
            "phone" => "08979276362",
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'Unauthorized'
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            "authorization" => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'ahmad',
                    'last_name' => 'ihsan',
                    'email' => 'ahmad@gmail.com',
                    'phone' => '111111',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            "authorization" => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }
    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, headers: [
            'authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testUpdateContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'hanif',
            'last_name' => 'hizbul',
            'email' => 'hanif@gmail.com',
            'phone' => '22222',
        ], [
            "authorization" => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'first_name' => 'hanif',
                    'last_name' => 'hizbul',
                    'email' => 'hanif@gmail.com',
                    'phone' => '22222',
                ]
            ]);
    }

    public function testUpdateContactFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'hizbul',
            'email' => 'hanif@gmail.com',
            'phone' => '22222',
        ], [
            "authorization" => 'test'
        ])->assertStatus(400)
            ->assertJson([
             "errors" => [
                "first_name" => [
                    "The first name field is required."
                ]
             ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->delete('/api/contacts/'. $contact->id, [], [
            'authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->delete('/api/contacts/'.( $contact->id+1), [], [
            'authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?name=first', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, ContactCollectionSeeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
    
}
