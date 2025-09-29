<?php

namespace Shopen\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Shopen\Models\User;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminUser extends Command
{
    protected $signature = 'shopen:create-admin-user';

    protected $description = 'Command description';

    public function handle()
    {
        $this->info('Creating Admin User...');
        $email = text(label: 'Email:', required: true);
        $firstName = text(label: 'First Name:', required: true);
        $lastName = text(label: 'Last Name:', required: true);
        $password = password(label: 'Password:', required: true);

        $admin = new User();
        $admin->email = $email;
        $admin->first_name = $firstName;
        $admin->last_name = $lastName;
        $admin->password = Hash::make($password);
        $admin->role = User::ROLE_ADMIN;
        $admin->save();

        $this->info('Admin User ' . $admin->email . ' has been created.');

    }
}
