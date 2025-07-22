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
            ['name' => 'view-students', 'description' => 'Can view students'],
            ['name' => 'create-students', 'description' => 'Can create students'],
            ['name' => 'edit-students', 'description' => 'Can edit students'],
            ['name' => 'delete-students', 'description' => 'Can delete students'],
            ['name' => 'view-roles', 'description' => 'Can view roles'],
            ['name' => 'create-roles', 'description' => 'Can create roles'],
            ['name' => 'edit-roles', 'description' => 'Can edit roles'],
            ['name' => 'delete-roles', 'description' => 'Can delete roles'],
            ['name' => 'assign-roles', 'description' => 'Can assign roles to users'],
            ['name' => 'unassign-roles', 'description' => 'Can unassign roles from users'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
