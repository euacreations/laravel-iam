# Laravel IAM Package

**Laravel IAM** is a lightweight, reusable **Identity and Access Management** package for Laravel applications.  
It provides **roles**, **permissions**, and automatic pivoting between them, making it easy to manage access control in your apps.  

Built for Laravel 12+, it integrates seamlessly with **Blade views**, **routes**, and **controllers**.

---

## **Features**

- Role-based access control (RBAC)
- Permission management
- Pivot table for role-permission relationships
- Built-in **admin role**
- Auto-assign new permissions to roles
- Middleware & Blade directives for easy authorization checks
- Fully reusable across Laravel projects

---

## **Installation**

Install via Composer (from local dev or GitHub):

```bash
composer require euacreations/laravel-iam:@dev-main
Publish the configuration file (optional):

php artisan vendor:publish --tag=iam-config
Minimal Integration Checklist
Follow these steps to integrate IAM into your Laravel application:

1️⃣ Run Migrations
php artisan migrate
php artisan iam:install
Creates roles, permissions, and role_permission tables

Adds a built-in admin role

Seeds default permissions/features

2️⃣ Add Role to Users Table
Add a foreign key to link users to roles:

$table->foreignId('role_id')->nullable()->constrained('iam_roles');
Run migration: php artisan migrate

3️⃣ Add Trait to User Model
Add the HasPermissions trait in App\Models\User.php:

use EuaCreations\LaravelIam\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasPermissions;
}
Provides:

$user->hasRole('admin')

$user->can('profile.edit')

4️⃣ Wire Laravel Gates
Edit app/Providers/AuthServiceProvider.php:

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'can')) {
                return $user->can($ability) ? true : null;
            }
        });
    }
}
Ensures @can Blade directives and $this->authorize() calls work with IAM permissions.

5️⃣ Protect Routes
Use the can: middleware to protect routes:

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'can:dashboard.view'])->name('dashboard');
Permission slugs correspond to can: middleware.

6️⃣ Protect Blade Views
Example of using Blade directives:

@can('profile.edit')
    <a href="{{ route('profile.edit') }}">Edit Profile</a>
@endcan
Only users with the correct permission will see the content.

7️⃣ Seed Initial Permissions
Add your first permission (feature):

php artisan iam:permission-create profile.edit "Edit Profile" --group=profile
If auto_assign_new_permissions is enabled, the admin role will automatically get the permission.

8️⃣ Test Your Integration
Log in as an admin user

Verify that permissions work in routes, controllers, and Blade

Add new roles and permissions as your application grows

Artisan Commands
Command	Description
php artisan iam:install	Installs the default IAM setup (roles, admin, features)
php artisan iam:permission-create {slug} {name} --group={group}	Create a new permission/feature
Usage Examples
Check Role
if (auth()->user()->hasRole('admin')) {
    // Show admin panel
}
Check Permission
if (auth()->user()->can('profile.edit')) {
    // Allow editing profile
}
Blade Directives
@can('profile.edit')
    <button>Edit Profile</button>
@endcan
Database Tables
iam_roles – Stores roles (admin, user, etc.)

iam_permissions – Stores permissions/features (profile.edit, etc.)

role_permission – Pivot table connecting roles to permissions

User table must have role_id for one-to-many relationship

License
MIT License
