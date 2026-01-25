# Laravel IAM

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-brightgreen)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/Laravel-%5E10.0-orange)](https://laravel.com/)

> **Feature-based Identity & Access Management for Laravel**  
> Username-first authentication • Single-role-per-user • Auto-synced capabilities

---

## Overview

Laravel IAM is a **feature-first, action-driven authorization system** for Laravel.  
It focuses on capabilities (Features) instead of arbitrary permission labels, ensuring that **system capabilities are always in sync with your codebase**.

Key principles:
- Users have **exactly one role**
- Features correspond to **actual system actions** (controllers/routes)
- Admin role automatically has all features
- Automatic sync for new features added to the system

---

## Core Concepts

| Concept | Description |
|--------|-------------|
| **Feature** | A concrete system action, e.g., `subscriber.create` |
| **Role** | A bundle of features assigned to a user |
| **User** | A single identity that authenticates using username (email optional) |
| **Admin** | Built-in user with all features and root privileges |

---

## Features

- ✅ Feature-based authorization (action-driven)
- ✅ Username-first authentication
- ✅ Single role per user
- ✅ Automatic feature discovery from routes
- ✅ Automatic feature assignment to Admin and roles with auto-assign enabled
- ✅ Laravel Gate & Blade integration
- ✅ Framework-agnostic, no UI

---

## Installation

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

## Database Schema Overview

```text
users
 └─ role_id

roles
 └─ auto_assign_new_features

features
 └─ key (subscriber.create)

role_feature
```

---

## Defining Features

Features are automatically defined from **routes using middleware**.

```php
Route::post('/subscriber', [SubscriberController::class, 'store'])
    ->middleware('feature:subscriber.create');
```

Run the Artisan command to sync features:

```bash
php artisan iam:sync-features
```

This will:
- Register missing features
- Assign them to Admin
- Assign to roles with `auto_assign_new_features = true`

---

## Authorization Usage

### Middleware

```php
Route::delete('/subscriber/{id}', [SubscriberController::class, 'destroy'])
    ->middleware('feature:subscriber.delete');
```

### Gates

```php
Gate::allows('subscriber.delete');
```

### Blade

```blade
@can('subscriber.delete')
    <button>Delete</button>
@endcan
```

---

## Users

Users authenticate using **username** (email optional).  
External providers (Google, Facebook) map their email to username.

- Local password optional
- Users can log in with provider, local password, or both

Root Admin example:
```
username: admin
password: <secure>
```
- Cannot be deleted
- Always has all features

---

## Philosophy

Laravel IAM follows a **capability-first model**, similar to:
- AWS IAM
- Kubernetes RBAC
- OAuth scopes

Capabilities → Roles → Users.

---

## Roadmap

- [ ] API token support
- [ ] Service accounts
- [ ] Policy bridge
- [ ] Audit logs

---

## License

MIT

