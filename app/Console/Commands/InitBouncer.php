<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Bouncer;

class InitBouncer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init:bouncer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialise roles and abilities';

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
     */
    public function handle()
    {
        // Define roles
        $admin = Bouncer::role()->create([
            'name' => 'admin',
            'title' => 'Administrator',
        ]);

        $staff = Bouncer::role()->create([
            'name' => 'staff',
            'title' => 'Staff',
        ]);

        $member = Bouncer::role()->create([
            'name' => 'member',
            'title' => 'Member',
        ]);

        // Define abilities
        $manageUsers = Bouncer::ability()->create([
            'name' => 'manage-users',
            'title' => 'Manage Users',
        ]);

        $manageMovies = Bouncer::ability()->create([
            'name' => 'manage-movies',
            'title' => 'Manage Movies',
        ]);

        $viewMovies = Bouncer::ability()->create([
            'name' => 'view-movies',
            'title' => 'View Movies',
        ]);

        // Assign abilities to roles
        Bouncer::allow($admin)->to($manageUsers);

        Bouncer::allow($staff)->to($manageMovies);
        Bouncer::allow($staff)->to($viewMovies);

        Bouncer::allow($member)->to($viewMovies);


        // Assign role to users
        $user = User::where('email', 'admin@mymovie.info')->first();
        Bouncer::assign($admin)->to($user);
        echo "Assign $admin->name to $user->name successfully\n";

        $user = User::where('email', 'staff@mymovie.info')->first();
        Bouncer::assign($staff)->to($user);
        echo "Assign $admin->name to $user->name successfully\n";

        $user = User::where('email', 'staff2@mymovie.info')->first();
        Bouncer::assign($staff)->to($user);
        echo "Assign $admin->name to $user->name successfully\n";

        $user = User::where('email', 'member@mymovie.info')->first();
        Bouncer::assign($member)->to($user);
        echo "Assign $admin->name to $user->name successfully\n";

    }
}
