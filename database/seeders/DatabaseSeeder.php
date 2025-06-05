<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->call([
            \App\Packages\Framework\Core\Database\Seeders\DatabaseSeeder::class,
        ]);

        $this->call([
            \App\Packages\Framework\Attribute\Database\Seeders\DatabaseSeeder::class,
        ]);

        $this->call([
            \App\Packages\User\Database\Seeders\DatabaseSeeder::class,
        ]);

        $this->call([
            \App\Packages\Page\Database\Seeders\DatabaseSeeder::class,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
