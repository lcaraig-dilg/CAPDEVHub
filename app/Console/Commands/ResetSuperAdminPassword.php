<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class ResetSuperAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-super-admin-password {--password= : Set a specific password instead of generating one}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the Super Admin password (prints the new password once).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::where('role', 'super_admin')->first();

        if (!$user) {
            $this->error('No Super Admin user found (role = super_admin).');
            return self::FAILURE;
        }

        $newPassword = $this->option('password');

        if (empty($newPassword)) {
            $newPassword = Str::random(16) . '!' . random_int(1000, 9999);
        }

        // User model has password cast 'hashed', so this will be stored securely.
        $user->password = $newPassword;
        $user->save();

        $this->line('═══════════════════════════════════════════════════════════');
        $this->info('SUPER ADMIN PASSWORD RESET');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->info('Email: ' . $user->email);
        $this->info('Username: ' . ($user->username ?? '(null)'));
        $this->warn('New Password: ' . $newPassword);
        $this->line('═══════════════════════════════════════════════════════════');
        $this->newLine();
        $this->comment('Store this password securely and change it after first login.');

        return self::SUCCESS;
    }
}
