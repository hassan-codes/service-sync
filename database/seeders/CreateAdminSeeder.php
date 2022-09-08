<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'password_reset_at' => Carbon::now(),
        ]);
    }
}
