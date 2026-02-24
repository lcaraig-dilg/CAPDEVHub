# Project Prompt & Instructions

**IMPORTANT: Read this file first before making any code changes or analysis.**

## Project Overview
**CAPDEVhub** is a Capacity Development Hub Web Application for the **Local Government Capability Development Division (LGCDD)** of the **Department of the Interior and Local Government - National Capital Region (DILG NCR)**.

This is a government website that stores and manages details of capacity development events organized by the LGCDD. The application serves as a centralized platform for tracking, managing, and organizing capability development activities for local government units in the NCR.

**Technical Stack**: Laravel 12 application using the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire).

## Tech Stack Details
- Laravel Framework: ^12.0
- Livewire: ^4.1 (includes Alpine.js automatically)
- Livewire Flux Pro: ^2.12
- Tailwind CSS: ^4.0.0
- Vite: ^7.0.7
- PHP: ^8.2

## Key Principles
1. **Always check existing code** before creating new functionality
2. **Follow Laravel conventions** - use artisan commands, follow naming conventions
3. **Prefer Livewire components** for dynamic, interactive features
4. **Use Alpine.js** for simple client-side interactions (already included with Livewire)
5. **Use Tailwind CSS** for all styling (Tailwind 4.0 syntax)
6. **Test changes** when possible

## User Roles & Permissions
The application has three user roles with different access levels:

1. **User (Client)**
   - Regular users/clients of the application
   - Can view and access public information
   - Limited access to data

2. **Admin**
   - Controls creation of data
   - Can create, edit, and manage capacity development event details
   - Cannot manipulate user data

3. **Super Admin**
   - Controls creation of data (same as Admin)
   - Can manipulate user data (create, edit, delete users)
   - Full administrative access to the system

**Important**: Always implement role-based access control (RBAC) when creating features. Check user roles before allowing access to sensitive operations.

## Project-Specific Rules
- **Government Application**: This is a government website - prioritize security, data privacy, and compliance
- **Purpose**: Store and manage capacity development event details for LGCDD
- **Users**: Government employees (Users), administrators (Admin), and super administrators (Super Admin)
- Database: SQLite (database/database.sqlite)
- Use Livewire Flux Pro components when available
- Follow PSR-12 coding standards
- Use Laravel's built-in features before adding external packages
- Consider accessibility requirements for government websites
- Implement proper user authentication and authorization with role-based access control

## Common Commands
```bash
# Development
composer dev          # Start all dev services
npm run dev          # Start Vite dev server
php artisan serve    # Start Laravel server

# Database
php artisan migrate
php artisan migrate:fresh --seed

# Livewire
php artisan make:livewire ComponentName
```

## File Locations
- Livewire Components: `app/Livewire/`
- Blade Views: `resources/views/`
- Routes: `routes/web.php`
- Frontend JS: `resources/js/app.js`
- Frontend CSS: `resources/css/app.css`

## Before You Code
1. ✅ Read this prompt
2. ✅ Understand the existing codebase
3. ✅ Check for similar functionality
4. ✅ Plan your approach
5. ✅ Follow conventions

---

**Update this file as your project evolves to include project-specific context and rules.**
