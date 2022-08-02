<?php

namespace Database\Seeders;

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
        \App\Models\User::factory()->create([
            'name' => 'ropco',
            'email' => 'info@ropco.ir',
            'email_verified_at' => null,
            'password' => '$2y$10$6781/mW3MLmCRilyJI3PueGjiTYtwBAD5YJvllRleWcvy4f5hBgSO', //Admin1234
            'remember_token' => '6jR67t3F5AmLjlpryTQHjssICjjH7frOamYaH72sXnSbwsR7PTHgfjoRlDp2',
        ]);

        // \App\Models\User::factory(10)->create();
    }
}
