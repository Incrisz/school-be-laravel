<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Students', 'description' => 'Can view students'],
            ['name' => 'Create Students', 'description' => 'Can create students'],
            ['name' => 'Edit Students', 'description' => 'Can edit students'],
            ['name' => 'Delete Students', 'description' => 'Can delete students'],
            ['name' => 'View Roles', 'description' => 'Can view roles'],
            ['name' => 'Create Roles', 'description' => 'Can create roles'],
            ['name' => 'Edit Roles', 'description' => 'Can edit roles'],
            ['name' => 'Delete Roles', 'description' => 'Can delete roles'],
            ['name' => 'Assign Roles', 'description' => 'Can assign roles to users'],
            ['name' => 'Unassign Roles', 'description' => 'Can unassign roles from users'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
