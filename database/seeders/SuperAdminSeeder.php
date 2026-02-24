<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        try {
            $existingSuperAdmin = User::where('role', 'super_admin')->first();
            
            if ($existingSuperAdmin) {
                $this->command->info('Super Admin already exists. Skipping...');
                return;
            }
        } catch (\Exception $e) {
            // If role column doesn't exist, continue with creation
            $this->command->warn('Note: Role column check failed, proceeding with creation...');
        }

        // Get credentials from environment variables or generate secure defaults
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@capdevhub.dilg-ncr.gov.ph');
        $username = env('SUPER_ADMIN_USERNAME', 'Super Administrator');
        
        // Generate a secure random password if not provided in .env
        // If SUPER_ADMIN_PASSWORD is set in .env, use it; otherwise generate one
        $password = env('SUPER_ADMIN_PASSWORD');
        
        if (empty($password)) {
            // Generate a secure random password
            $password = Str::random(16) . '!' . rand(1000, 9999);
            $this->command->warn('⚠️  WARNING: No SUPER_ADMIN_PASSWORD set in .env file!');
            $this->command->warn('⚠️  A random password has been generated. Save it securely!');
            $this->command->newLine();
            $this->command->line('═══════════════════════════════════════════════════════════');
            $this->command->warn('  SUPER ADMIN CREDENTIALS - SAVE THIS INFORMATION!');
            $this->command->line('═══════════════════════════════════════════════════════════');
            $this->command->info('  Username: ' . $username);
            $this->command->info('  Email: ' . $email);
            $this->command->error('  Password: ' . $password);
            $this->command->line('═══════════════════════════════════════════════════════════');
            $this->command->newLine();
            $this->command->warn('⚠️  IMPORTANT: Add this to your .env file:');
            $this->command->line('   SUPER_ADMIN_PASSWORD=' . $password);
            $this->command->warn('⚠️  Change this password immediately after first login!');
            $this->command->newLine();
        } else {
            $this->command->info('✓ Using password from .env file');
        }

        // Create Super Admin
        $user = User::create([
            'first_name' => 'Super',
            'middle_initial' => null,
            'last_name' => 'Administrator',
            'suffix' => null,
            'gender' => 'Prefer not to say',
            'date_of_birth' => Carbon::now()->subYears(30),
            'age' => 30,
            'is_pwd' => false,
            'requires_assistance' => null,
            'office' => 'DILG NCR - LGCDD',
            'position' => 'Super Administrator',
            'lgu_organization' => 'DILG NCR',
            'contact_number' => '00000000000',
            'email' => $email,
            'dietary_restrictions' => null,
            'username' => $username,
            'name' => 'Super Administrator',
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        if (env('SUPER_ADMIN_PASSWORD')) {
            $this->command->info('✓ Super Admin created successfully!');
            $this->command->info('  Username: ' . $username);
            $this->command->info('  Email: ' . $email);
            $this->command->warn('  Password: (set in .env file)');
            $this->command->warn('  ⚠️  Change password after first login!');
        }
    }
}
