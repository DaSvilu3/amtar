<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--name= : The name of the admin user}
                            {--email= : The email address of the admin user}
                            {--password= : The password for the admin user (min 8 characters)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an administrator user for the system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Creating Administrator User');
        $this->newLine();

        // Get or prompt for name
        $name = $this->option('name') ?? $this->ask('Enter admin name');

        // Get or prompt for email
        $email = $this->option('email') ?? $this->ask('Enter admin email');

        // Get or prompt for password
        $password = $this->option('password');
        if (!$password) {
            $password = $this->secret('Enter admin password (min 8 characters)');
        }

        // Validate inputs
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }

        // Check if administrator role exists
        $adminRole = Role::where('slug', 'administrator')->first();
        if (!$adminRole) {
            $this->error('Administrator role not found. Please run the RoleSeeder first:');
            $this->line('  php artisan db:seed --class=RoleSeeder');
            return Command::FAILURE;
        }

        // Create the user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Assign administrator role
        $user->roles()->attach($adminRole->id);

        $this->newLine();
        $this->info('Administrator user created successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $user->name],
                ['Email', $user->email],
                ['Role', 'Administrator'],
                ['Status', 'Active'],
            ]
        );

        $this->newLine();
        $this->warn('Please save your login credentials securely.');
        $this->line('Login URL: ' . config('app.url') . '/login');

        return Command::SUCCESS;
    }
}
