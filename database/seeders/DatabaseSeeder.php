<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Folder;
use Illuminate\Support\Facades\Hash;
use App\Models\Set;
use App\Models\Flashcard;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Alice Kowalska',
                'email' => 'alice@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Bob Nowak',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Charlie Wiśniewski',
                'email' => 'charlie@example.com',
                'password' => Hash::make('password123'),
            ],
        ])->map(function ($user) {
            return User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => $user['password'],
                'email_verified_at' => now(),
            ]);
        });
        
        Folder::factory()
            ->count(3)
            ->create()
            ->each(function ($folder) use ($users) {
                $folder->user_id = $users->random()->id;
                $folder->save();

                Set::factory()
                    ->count(2)
                    ->create([
                        'folder_id' => $folder->id,
                    ])
                    ->each(function ($set) {
                        Flashcard::factory()
                            ->count(15)
                            ->create([
                                'set_id' => $set->id,
                            ]);

                        Flashcard::factory()
                            ->count(5)
                            ->learned()
                            ->create([
                                'set_id' => $set->id,
                            ]);
                    });
            });
    }
}
