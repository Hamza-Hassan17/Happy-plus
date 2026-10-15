<?php

namespace Database\Seeders;

use App\Models\Animation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class AnimationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * The video files are not in git; upload them to public/uploads/animations/videos first.
     */
    public function run(): void
    {
        $animations = [
            ['name' => 'Daisy', 'video' => 'uploads/animations/videos/daisy.mp4'],
            ['name' => 'Lily (Fox)', 'video' => 'uploads/animations/videos/lily-fox.mp4'],
            ['name' => 'Oscar (Dog)', 'video' => 'uploads/animations/videos/oscar-dog.mp4'],
            ['name' => 'Rocket (Dog)', 'video' => 'uploads/animations/videos/rocket-dog.mov'],
            ['name' => 'Luna (Unicorn)', 'video' => 'uploads/animations/videos/unicorn.mp4'],
        ];

        foreach ($animations as $animation) {
            $path = public_path($animation['video']);

            Animation::updateOrCreate(['video' => $animation['video']], [
                'name' => $animation['name'],
                'video_size' => File::exists($path) ? File::size($path) : null,
            ]);
        }
    }
}
