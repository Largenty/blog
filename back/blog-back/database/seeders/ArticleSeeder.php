<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        for($i = 0; $i < 20; $i++) {
            $user = $users->random();
            Article::factory()->create([
                'user_id' => $user->id
            ]);
        }
    }
}
