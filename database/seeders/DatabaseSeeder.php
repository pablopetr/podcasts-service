<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Show;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $show = Show::firstOrCreate(
            ['slug'=>'tech-cast'],
            ['title'=>'Tech Cast','description'=>'Tech news & interviews','image_url'=>null]
        );

        foreach (range(1,5) as $i) {
            Episode::updateOrCreate(
                ['slug'=>"ep-$i"],
                [
                    'show_id'=>$show->id,
                    'title'=>"Episode $i",
                    'description'=>"Topic $i",
                    'duration_sec'=>rand(600,3600),
                    'audio_url'=>"https://cdn.example.com/audio/ep$i.mp3",
                    'published_at'=>now()->subDays(7-$i),
                ]
            );
        }
    }
}
