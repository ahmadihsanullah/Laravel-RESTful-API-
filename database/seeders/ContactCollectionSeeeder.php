<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ContactCollectionSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                'first_name' => 'first ' . $i,
                'last_name' => 'last ' . $i,
                'email' => 'test' . $i . '@pzn.com',
                'phone' => '11111' . $i,
                'user_id' => $user->id
            ]);
        }
    }
}
