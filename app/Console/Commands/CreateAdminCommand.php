<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {--login_id=admin : Admin login ID} {--email=admin@example.com : Admin email} {--password= : Admin password} {--force : Force creation even if admin exists}';
    
    protected $description = 'Create a new admin account';

    public function handle()
    {
        $loginId = $this->option('login_id');
        $email = $this->option('email');
        $password = $this->option('password') ?: Str::random(12);
        $force = $this->option('force');

        if (!$force && Admin::where('email', $email)->exists()) {
            $this->error("Admin with email {$email} already exists!");
            
            if ($this->confirm('Do you want to create a new admin with different credentials?')) {
                $email = $this->ask('Enter admin email:');
                $loginId = $this->ask('Enter admin login ID:');
                $password = $this->secret('Enter admin password (leave empty for random):') ?: Str::random(12);
            } else {
                return 1;
            }
        }

        $admin = Admin::create([
            'admin_uuid' => (string) Str::uuid(),
            'admin_login_id' => $loginId,
            'email' => $email,
            'password' => Hash::make($password),
            'remember_token' => Str::random(10),
        ]);

        $this->info('Admin account created successfully!');
        $this->table(
            ['Login ID', 'Email', 'Password'],
            [[$admin->admin_login_id, $admin->email, $password]]
        );
        
        $this->info("Please save this password securely - it won't be shown again!");
        
        return 0;
    }
}