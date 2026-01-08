<?php

namespace Shopen\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Shopen\Models\User;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;

class SetupAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shopen:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = text(label: 'Email:', required: true);
        $name = text(label: 'Name:', required: true);
        $password = password(label: 'Password:', required: true);

        $admin = new User();
        $admin->email = $email;
        $admin->first_name = $name;
        $admin->password = Hash::make($password);
        $admin->save();

        info('Admin account has been created');

    }
}
