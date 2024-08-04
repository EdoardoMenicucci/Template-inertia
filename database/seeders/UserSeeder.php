<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!User::where("email", "luca@lambia.it")->first()) {
            $mainUser = new User();
            $mainUser->name = "Edoardo";
            $mainUser->email = "edo@example.com";
            $mainUser->password = Hash::make('admin');
            $mainUser->assignRole('admin');
            $mainUser->save();
        } // password('admin');
    }
}
