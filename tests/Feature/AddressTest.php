<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => "test_street",
            'city' => "test_city",
            'province' => "test_prov",
            'country' => "test_country",
            'postal_code' => "test_pc",
        ], [
            'authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    'street' => "test_street",
                    'city' => "test_city",
                    'province' => "test_prov",
                    'country' => "test_country",
                    'postal_code' => "test_pc",
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            'street' => "test_street",
            'city' => "test_city",
            'province' => "test_prov",
            'country' => "",
            'postal_code' => "test_pc",
        ], [
            'authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'country' => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    public function testContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::limit(1)->first();

        $this->post('/api/contacts/' . ($contact->id + 1) . '/addresses', [
            'street' => "test_street",
            'city' => "test_city",
            'province' => "test_prov",
            'country' => "test_country",
            'postal_code' => "test_pc",
        ], [
            'authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $addreess = Address::limit(1)->first();

        $this->get("/api/contacts/" . $addreess->contact_id . "/addresses/" . $addreess->id, [
            'authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'test_street',
                    'city' => 'test_city',
                    'province' => 'test_province',
                    'country' => 'test_country',
                    'postal_code' => '11111',
                ]
            ]);
    }

    public function testGetFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $addreess = Address::limit(1)->first();

        $this->get("/api/contacts/" . $addreess->contact_id . "/addresses/" . ($addreess->id + 1), [
            'authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "address not found"
                    ]
                ]
            ]);
    }

    public function testAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    'street' => 'update',
                    'city' => 'update',
                    'province' => 'update',
                    'country' => 'update',
                    'postal_code' => '22222'
                ]
            ]);
    }

    public function testAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => '',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'country' => ['The country field is required.']
                ]
            ]);
    }

    public function testAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::limit(1)->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [
                'street' => 'update',
                'city' => 'update',
                'province' => 'update',
                'country' => 'update',
                'postal_code' => '22222'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['address not found']
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1),
            [],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['address not found']
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'street' => 'test_street',
                        'city' => 'test_city',
                        'province' => 'test_province',
                        'country' => 'test_country',
                        'postal_code' => '11111',
                    ]
                ]
            ]);
    }

    public function testListContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get(
            '/api/contacts/' . ($contact->id+1) . '/addresses',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                'errors' => [
                        'message' => ["not found"]
                ]
            ]);
    }
}
