<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Folder;
use App\Models\Set;
use App\Models\Flashcard;

class ProdSeeder extends Seeder
{
    public function run(): void
    {
        Folder::factory()
            ->count(3)
            ->create()
            ->each(function ($folder) {
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
