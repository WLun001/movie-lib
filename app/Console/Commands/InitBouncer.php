<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Silber\Bouncer\Bouncer;

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
        $manageStudios = Bouncer::ability()->create([
            'name' => 'manage-studios',
            'title' => 'Manage Studios',
        ]);
        $manageActors = Bouncer::ability()->create([
            'name' => 'manage-actors',
            'title' => 'Manage Actors',
        ]);

        $viewMovies = Bouncer::ability()->create([
            'name' => 'view-movies',
            'title' => 'View Movies',
        ]);
        $viewStudios = Bouncer::ability()->create([
            'name' => 'view-movies',
            'title' => 'View Movies',
        ]);
        $viewActors = Bouncer::ability()->create([
            'name' => 'view-movies',
            'title' => 'View Movies',
        ]);

        // Assign abilities to roles
        Bouncer::allow($admin)->to($manageUsers);

        Bouncer::allow($staff)->to($manageMovies);
        Bouncer::allow($staff)->to($manageStudios);
        Bouncer::allow($staff)->to($manageActors);

        Bouncer::allow($member)->to($viewMovies);
        Bouncer::allow($member)->to($viewStudios);
        Bouncer::allow($member)->to($viewActors);


        // Assign role to users
        $user = User::where('email', 'admin@mylib.info')->first();
        Bouncer::assign($admin)->to($user);

        $user = User::where('email', 'user1@mylib.info')->first();
        Bouncer::assign($staff)->to($user);

        $user = User::where('email', 'user2@mylib.info')->first();
        Bouncer::assign($member)->to($user);

    }
}
