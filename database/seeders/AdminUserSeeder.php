<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'JellO',
            'email' => 'jello@gmail.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
            'first_name'=>'Ouis',
            'last_name'=> 'Jello',
            'phone'=> '714451361',
            'address' => '60street-sana\'a',
            
        ]);
    }
}