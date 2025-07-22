# Applying Permissions to Controllers

This document explains how to apply the new permission-based access control to your controllers.

## 1. Protecting Routes

You can protect your routes by applying the `permission` middleware in your `routes/api.php` file. The middleware takes the name of the permission as an argument.

For example, to protect the `index` method of the `StudentController` so that only users with the `view-students` permission can access it, you would do the following:

```php
Route::get('students', [\App\Http\Controllers\Api\V1\StudentController::class, 'index'])->middleware('permission:view-students');
```

You can also protect all the methods in a resource controller like this:

```php
Route::apiResource('students', \App\Http\Controllers\Api\V1\StudentController::class)->middleware('permission:manage-students');
```

In this case, you would need to create a `manage-students` permission.

## 2. Available Permissions

The following permissions are available by default:

- `view-students`
- `create-students`
- `edit-students`
- `delete-students`
- `view-roles`
- `create-roles`
- `edit-roles`
- `delete-roles`
- `assign-roles`
- `unassign-roles`

You can add more permissions by editing the `database/seeders/PermissionSeeder.php` file and running the seeder.

## 3. Creating Roles and Assigning Permissions

You can create roles and assign permissions to them using the `/api/v1/roles` endpoints.

**Example: Create a "Teacher" role with permission to view students**

**Request:**

```json
POST /api/v1/roles
{
  "name": "Teacher",
  "description": "Teacher role",
  "permissions": ["view-students"]
}
```

**Response:**

```json
{
  "id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
  "name": "Teacher",
  "description": "Teacher role",
  "created_at": "2025-07-22T15:56:59.000000Z",
  "updated_at": "2025-07-22T15:56:59.000000Z",
  "permissions": [
    {
      "id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx",
      "name": "View Students",
      "description": "Can view students",
      "created_at": "2025-07-22T15:56:59.000000Z",
      "updated_at": "2025-07-22T15:56:59.000000Z"
    }
  ]
}
```

## 4. Assigning Roles to Users

You can assign roles to users using the `/api/v1/users/{user}/assign-role` endpoint.

**Example: Assign the "Teacher" role to a user**

**Request:**

```json
POST /api/v1/users/xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx/assign-role
{
  "role_id": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
}
```

**Response:**

```json
{
  "message": "Role assigned successfully."
}
```
