<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeMasterUser extends Command
{
    protected $signature = 'master:user
                            {--email= : Email address of the user}
                            {--name= : Display name}
                            {--password= : Password (prompted if omitted)}
                            {--promote : Promote an existing user by email instead of creating one}';

    protected $description = 'Create or promote a user to the master role';

    public function handle(): int
    {
        $promote = $this->option('promote');
        $email   = $this->option('email') ?? $this->ask('Email address');

        if ($promote) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->error("No user found with email: {$email}");
                return self::FAILURE;
            }

            $user->organization_id = null;
            $user->save();
            $user->syncRoles('master');

            $this->info("User [{$user->name}] promoted to master.");
            return self::SUCCESS;
        }

        $name     = $this->option('name') ?? $this->ask('Display name');
        $password = $this->option('password') ?? $this->secret('Password');

        $user = User::create([
            'name'            => $name,
            'email'           => $email,
            'password'        => Hash::make($password),
            'organization_id' => null,
        ]);

        $user->assignRole('master');

        $this->info("Master user [{$user->email}] created successfully.");
        $this->line("Login at: " . url('/master'));

        return self::SUCCESS;
    }
}
