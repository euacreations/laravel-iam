# Laravel IAM

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-brightgreen)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-%5E12.0-orange)](https://laravel.com/)

> **Feature-based Identity & Access Management for Laravel**  

---

## Overview

Laravel IAM is a feature-first, action-driven authorization system for Laravel. It focuses on **capabilities** (Features) instead of arbitrary permission labels, ensuring that system capabilities are always in sync with your codebase.

### Key Principles

* Users have exactly one role
* Features correspond to actual system actions (controllers/routes)
* Admin role automatically has all features
* Automatic sync for new features added to the system

---

## Core Concepts

| Concept | Description |
|---------|-------------|
| **Feature** | A concrete system action, e.g., `subscriber.create` |
| **Role** | A bundle of features assigned to a user |
| **User** | A single identity that authenticates using username (email optional) |
| **Admin** | Built-in user with all features and root privileges |

---

## Features

* ✅ Feature-based authorization (action-driven)
* ✅ Username-first authentication
* ✅ Single role per user
* ✅ Automatic feature discovery from routes
* ✅ Automatic feature assignment to Admin and roles with auto-assign enabled
* ✅ Laravel Gate & Blade integration
* ✅ Framework-agnostic, no UI
* ✅ Multi-provider authentication support (local, OAuth)

---

## Minimal Integration Checklist

1. Install package via Composer
2. Run migrations
3. Add `role_id` column to `users` table
4. Ensure `User` model uses IAM trait
5. Define features using `feature` middleware (route name = feature slug)
6. Run `php artisan iam:sync-features`
7. Assign roles to users
8. Use `@can`, `Gate`, or middleware for authorization

---

## Installation

Install via Composer:

```bash
composer require euacreations/laravel-iam
```

Publish config (optional):

```bash
php artisan vendor:publish --tag=iam-config
```

Run migrations:

```bash
php artisan migrate
```

---

## Minimal Installation Requirements

Before running `iam:install`, ensure:

1. At least one user exists in your application.
   - You can verify with:
     ```bash
     php artisan iam:user-list
     ```
2. Your `users` table has a `role_id` column.
3. Your `User` model uses the IAM trait.

If no users exist, `iam:install` will stop and ask you to create the first user.

## Add `role_id` to Users

Create a migration to add the `role_id` column:

```php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('role_id')->nullable()->constrained('iam_roles');
});
```

## Add IAM Trait to User

```php
use EuaCreations\LaravelIam\Traits\HasFeatures;

class User extends Authenticatable
{
    use HasFeatures;

    public function role()
    {
        return $this->belongsTo(\EuaCreations\LaravelIam\Models\Role::class);
    }
}
```

---

## Quick Start

### 1. Define Features in Routes

Features are automatically defined from routes using middleware. When you use
`feature` without a parameter, the **route name becomes the feature slug**.

```php
Route::post('/subscriber', [SubscriberController::class, 'store'])
    ->name('subscriber.create')
    ->middleware('feature');

Route::delete('/subscriber/{id}', [SubscriberController::class, 'destroy'])
    ->name('subscriber.delete')
    ->middleware('feature');
```

You can still pass an explicit slug:

```php
Route::post('/subscriber', [SubscriberController::class, 'store'])
    ->middleware('feature:subscriber.create');
```

### 2. Sync Features

Run the Artisan command to discover and register features:

```bash
php artisan iam:sync-features
```

This will:
* Register missing features from your routes
* Automatically assign them to Admin
* Assign to roles with `auto_assign_new_features = true`

### 3. Create Roles and Assign Features

```php
use EuaCreations\LaravelIam\Models\Role;
use EuaCreations\LaravelIam\Models\Feature;

// Create a role
$role = Role::create([
    'name' => 'Content Manager',
    'auto_assign_new_features' => false
]);

// Assign specific features to role
$createFeature = Feature::where('slug', 'subscriber.create')->first();
$role->features()->attach($createFeature);

// Assign user to role
$user->update(['role_id' => $role->id]);
```

### 4. Check Authorization

Use Laravel's native authorization methods:

```php
// In controllers
if (Gate::allows('subscriber.delete')) {
    // User can delete subscribers
}

// In Blade templates
@can('subscriber.create')
    <button>Create Subscriber</button>
@endcan

// Using middleware
Route::delete('/subscriber/{id}', [SubscriberController::class, 'destroy'])
    ->name('subscriber.delete')
    ->middleware('feature');
```

---

## Database Schema

```
users
 └─ role_id (foreign key)

roles
 ├─ name
 └─ auto_assign_new_features (boolean)

features
 ├─ slug (e.g., subscriber.create)
 ├─ name
 └─ description

role_has_features (pivot)
 ├─ role_id
 └─ feature_id
```

**Key Design Decisions:**
* One-to-many relationship between Role and User (single role per user)
* Many-to-many relationship between Role and Feature (flexible capability assignment)
* Admin user automatically bypasses all feature checks

---

## Authorization Usage

### Middleware

Protect routes with feature-based middleware:

```php
Route::post('/subscriber', [SubscriberController::class, 'store'])
    ->middleware('feature:subscriber.create');
```

### Gates

Check permissions programmatically:

```php
if (Gate::allows('subscriber.delete')) {
    // Authorized
}

Gate::authorize('subscriber.update');
```

### Blade Directives

Conditionally render UI elements:

```php
@can('subscriber.delete')
    <button class="btn-danger">Delete</button>
@endcan

@cannot('subscriber.edit')
    <p>You don't have permission to edit subscribers.</p>
@endcannot
```

### Policy Integration

Works seamlessly with Laravel Policies:

```php
// In SubscriberPolicy
public function delete(User $user, Subscriber $subscriber)
{
    return Gate::allows('subscriber.delete');
}
```

---

## User Authentication

Users authenticate using **username** (email is optional). This supports multiple authentication providers:

### Local Authentication
```php
username: john.doe
password: <secure>
```

### External Providers
OAuth providers (Google, Facebook, etc.) map their email to username:
```php
username: john.doe@gmail.com  // From Google OAuth
password: null                 // Local password optional
```

### Root Admin
Built-in admin user with special privileges:
```php
username: admin
password: <configured in seeder>
```

**Admin Capabilities:**
* Cannot be deleted
* Always has all features (bypasses feature checks)
* Automatically receives new features when synced

---

## Testing

Run the test suite:

```bash
composer test
```

### Current Test Coverage

The package includes a growing test suite using **Pest**

- ✅ Feature discovery and sync logic
- ✅ Role-feature assignment rules
- ✅ Admin privilege enforcement
- ✅ User authentication flows
- ✅ Gate and policy integration
- ✅ Auto-assignment behavior

### Running Specific Tests

```bash
# Run feature sync tests
./vendor/bin/pest --filter=FeatureSyncTest

# Run authorization tests
./vendor/bin/pest --filter=AuthorizationTest
```

---

## Architecture & Design Patterns

Laravel IAM follows domain-driven design principles and implements several proven patterns:

### Design Patterns

* **Repository Pattern**: Clean data access layer for models
* **Strategy Pattern**: Multiple authentication providers (local, OAuth)
* **Observer Pattern**: Auto-sync and auto-assignment of features
* **Factory Pattern**: Feature creation from route definitions
* **Policy Pattern**: Integration with Laravel's authorization system

### Domain-Driven Design

* **Bounded Context**: Authorization and authentication as separate concerns
* **Aggregate Root**: Role as the central entity for capability management
* **Value Objects**: Feature keys as immutable identifiers
* **Domain Events**: Feature sync triggers role updates

### Database Design

* Normalized schema with proper foreign key constraints
* Pivot table for many-to-many relationships
* Indexed columns for performance (feature keys, role IDs)
* Soft deletes support for audit trails

---

## Configuration

Publish and customize the configuration file:

```bash
php artisan vendor:publish --tag=iam-config
```

### Available Options

```php
return [
    // Admin username (cannot be changed after seeding)
    'admin_username' => env('IAM_ADMIN_USERNAME', 'admin'),
    
    // Feature key separator
    'feature_separator' => '.',
    
    // Auto-discover features on sync
    'auto_discover' => true,
    
    // Database table names
    'tables' => [
        'users' => 'users',
        'roles' => 'roles',
        'features' => 'features',
        'role_has_features' => 'role_has_features',
    ],
];
```

---

## Console Commands

### Core

```bash
php artisan iam:install
php artisan iam:sync-features
php artisan iam:feature-prune
```

### Roles

```bash
php artisan iam:role-create {slug} {name}
php artisan iam:role-assign {user} {role}
php artisan iam:role-feature {role} {features}
php artisan iam:role-list
php artisan iam:role-show {role}
```

### Features

```bash
php artisan iam:feature-create {slug} {name}
php artisan iam:feature-list
```

### Users

```bash
php artisan iam:user-list
```

---

## Philosophy

Laravel IAM follows a **capability-first** model, similar to enterprise IAM systems:

* **AWS IAM**: Actions → Policies → Roles → Users
* **Kubernetes RBAC**: Verbs → Rules → Roles → Subjects
* **OAuth 2.0**: Scopes → Clients → Users

**Core Philosophy:**
```
Capabilities → Roles → Users
```

Instead of asking "What permissions does this user have?", we ask:
> "What features can this user's role access?"

This ensures:
1. **Type Safety**: Features correspond to actual code (routes/controllers)
2. **Maintainability**: Adding a route automatically defines the capability
3. **Consistency**: System capabilities match application capabilities
4. **Auditability**: Clear mapping between code and authorization

---

## Roadmap

- [x] Core feature-based authorization
- [x] Automatic feature discovery from routes
- [x] Single-role-per-user model
- [x] Laravel Gate and Blade integration
- [x] Multi-provider authentication support
- [ ] Comprehensive test coverage expansion
- [ ] API token support (Sanctum integration)
- [ ] Service accounts (machine-to-machine auth)
- [ ] Policy bridge (Laravel policy generation)
- [ ] Audit logs and activity tracking
- [ ] Feature grouping and namespaces
- [ ] Role inheritance and hierarchies
- [ ] Time-based feature access (temporary permissions)

---

## Use Cases

### SaaS Applications
Perfect for multi-tenant SaaS where each customer needs different feature access:
```php
// Free tier
$freeRole = Role::create(['name' => 'Free User']);
$freeRole->features()->attach(['post.create', 'post.read']);

// Premium tier
$premiumRole = Role::create(['name' => 'Premium User']);
$premiumRole->features()->attach(['post.create', 'post.read', 'analytics.view', 'export.csv']);
```

### Enterprise Applications
Manage complex organizational hierarchies:
```php
// Department-specific capabilities
$hrRole = Role::create(['name' => 'HR Manager']);
$hrRole->features()->attach(['employee.create', 'employee.update', 'payroll.view']);

$financeRole = Role::create(['name' => 'Finance Manager']);
$financeRole->features()->attach(['invoice.create', 'payment.process', 'reports.financial']);
```

### Compliance Systems
Track and control access to sensitive operations:
```php
// Regulatory compliance
$auditorRole = Role::create(['name' => 'Auditor', 'auto_assign_new_features' => false]);
$auditorRole->features()->attach(['audit.view', 'report.generate']);
// New features require explicit approval for auditors
```

---

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

```bash
# Clone the repository
git clone https://github.com/euacreations/laravel-iam.git

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer analyse
```

---

## License

MIT

---

## Credits

Created by [Eranga Upul Amarakoon](https://github.com/euacreations)

Inspired by enterprise IAM systems and Laravel's elegant authorization system.
