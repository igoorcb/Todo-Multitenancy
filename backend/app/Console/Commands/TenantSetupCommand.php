<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantSetupCommand extends Command
{
    protected $signature = 'tenant:setup';

    protected $description = 'Create a new tenant with an admin user';

    public function handle(): int
    {
        $this->info('Tenant Setup');
        $this->newLine();

        $tenantName = $this->ask('Enter tenant name');
        $tenantDomain = $this->ask('Enter tenant domain');
        $tenantDatabase = $this->ask('Enter tenant database (optional)', 'main');

        $validator = Validator::make([
            'name' => $tenantName,
            'domain' => $tenantDomain,
            'database' => $tenantDatabase,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:tenants,domain'],
            'database' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        $tenant = Tenant::create([
            'name' => $tenantName,
            'domain' => $tenantDomain,
            'database' => $tenantDatabase,
        ]);

        $this->info("Tenant '{$tenant->name}' created successfully!");
        $this->newLine();

        $userName = $this->ask('Enter admin user name');
        $userEmail = $this->ask('Enter admin user email');
        $userPassword = $this->secret('Enter admin user password');
        $userPasswordConfirmation = $this->secret('Confirm admin user password');

        $userValidator = Validator::make([
            'name' => $userName,
            'email' => $userEmail,
            'password' => $userPassword,
            'password_confirmation' => $userPasswordConfirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($userValidator->fails()) {
            $this->error('User validation failed:');
            foreach ($userValidator->errors()->all() as $error) {
                $this->error($error);
            }

            $tenant->delete();
            $this->error('Tenant was deleted due to user creation failure.');

            return Command::FAILURE;
        }

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $userName,
            'email' => $userEmail,
            'password' => Hash::make($userPassword),
        ]);

        $this->newLine();
        $this->info("Admin user '{$user->name}' created successfully!");
        $this->newLine();
        $this->table(
            ['Tenant ID', 'Tenant Name', 'Domain', 'User ID', 'User Name', 'Email'],
            [
                [
                    $tenant->id,
                    $tenant->name,
                    $tenant->domain,
                    $user->id,
                    $user->name,
                    $user->email,
                ]
            ]
        );

        return Command::SUCCESS;
    }
}
