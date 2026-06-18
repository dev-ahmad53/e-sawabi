<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'base_poster' => 'base_poster.jpg',
            'photo_x' => '275',
            'photo_y' => '435',
            'photo_size' => '270',
            'name_x' => '410',
            'name_y' => '750',
            'font_size' => '28',
            'font_color' => '#ffffff',
            'font_family' => 'Arial-Bold.ttf',
        ];

        foreach ($defaults as $key => $value) {
            Setting::setVal($key, $value);
        }
    }
}
