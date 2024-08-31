<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Single;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'admin@examsedia.com',
            'password' => bcrypt('admin1234'),
            'nama_lengkap' => 'admin',
            'jenis_kelamin' => 'admin',
            'role' => 'admin'
        ]);
        Single::create([
            "name" => "registerStatus",
            "value" => "1"
        ]);
    }
}
