<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
        ]);

        $user = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'nic' => '973040521v',
            'contact_number' => '0775612358',
            'address' => "Kurunegala",
            'position' => "Super Admin",
            'email'=> "superadmin@example.com",
            'password' => Hash::make('admin123')
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);

        $permissions = Permission::all();
        $superAdmin->givePermissionTo($permissions);

        $user->assignRole($superAdmin);
    }
}
