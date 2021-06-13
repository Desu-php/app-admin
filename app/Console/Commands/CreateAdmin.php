<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Role::updateOrCreate([
            'name' => 'SuperAdmin',
            'guard_name' => 'web'
        ]);

        Role::updateOrCreate([
            'name' => 'Client',
            'guard_name' => 'web'
        ]);



        $firstUser = User::updateOrCreate(
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
            ],
            [
                'password' => Hash::make('Passw0rd'),
            ]
        );

        $firstUser->assignRole('SuperAdmin');

        $secondUser = User::updateOrCreate(
            [
                'name' => 'Administrator',
                'email' => 'ab@softjet.ru',
            ],
            [
                'password' => Hash::make('Passw0rd'),
            ]
        );

        $secondUser->assignRole('SuperAdmin');
        return 0;
    }
}
