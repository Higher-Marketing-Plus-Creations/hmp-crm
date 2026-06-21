<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin-user {name?} {email?} {password?}';

    protected $description = 'Create an admin user for the CRM dashboard';

    public function handle(): int
    {
        $name = $this->argument('name') ?: $this->ask('Admin name');
        $email = $this->argument('email') ?: $this->ask('Admin email');
        $password = $this->argument('password') ?: $this->secret('Admin password');

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
            ]
        );

        $this->info("Admin user ready: {$user->email}");

        return self::SUCCESS;
    }
}
