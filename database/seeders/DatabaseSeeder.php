<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();

        Ticket::factory(100)
            ->recycle($users)
            ->create();

        User::create([
            'name' => 'Test Example',
            'email' => 'test@example.com',
            'password' => 'password',
            'is_manager' => true
        ]);
    }
}
