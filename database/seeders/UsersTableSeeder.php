<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use DB;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get role IDs from the roles table
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $accountingRoleId = DB::table('roles')->where('name', 'accounting')->value('id');
        $salesRoleId = DB::table('roles')->where('name', 'sales')->value('id');

        // Insert users
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('123456'), 
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Accounting User',
                'email' => 'accounting@example.com',
                'password' => bcrypt('123456'), 
                'role_id' => $accountingRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales User 1',
                'email' => 'sales1@example.com',
                'password' => bcrypt('123456'),
                'role_id' => $salesRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sales User 2',
                'email' => 'sales2@example.com',
                'password' => bcrypt('123456'),
                'role_id' => $salesRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
