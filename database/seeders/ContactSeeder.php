<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();

        Contact::create([
            "first_name" => "ahmad",
            "last_name" => "ihsan",
            "email" => "ahmad@gmail.com",
            "phone" => "111111",
            "user_id" => $user->id
        ]);
    }
}
