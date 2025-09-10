<?php
use App\Models\User;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@calanalyzer.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }
}