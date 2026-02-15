<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key' => 'site_name',
            'value' => 'Koperasi NFBS Bogor',
        ]);

        Setting::create([
            'key' => 'site_tagline',
            'value' => 'Belanja Mudah, Cepat, dan Aman',
        ]);

        Setting::create([
            'key' => 'site_description',
            'value' => 'Koperasi NFBS Bogor',
        ]);

        Setting::create([
            'key' => 'site_logo',
            'value' => 'logo.png',
        ]);

        Setting::create([
            'key' => 'front_heading',
            'value' => 'Selamat Datang di Koperasi NFBS Bogor',
        ]);

        Setting::create([
            'key' => 'front_subheading',
            'value' => 'Belanja Mudah, Cepat, dan Aman',
        ]);

        Setting::create([
            'key' => 'front_slider',
            'value' => '[
                "slider1.jpg",
                "slider2.jpg",
                "slider3.jpg",
            ]',
        ]);

        Setting::create([
            'key' => 'whatsapp_number',
            'value' => '628123456789',
        ]);
        
        Setting::create([
            'key' => 'register_open',
            'value' => 0,
        ]);
    }
}
