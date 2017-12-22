<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UsersSeeder extends Seeder
{
    /**
     * seed authors and readers
     *
     * @return void
     */
    public function run()
    {
        //create 2 authors
        $users = factory(App\User::class, 2)->create();
        $role = Role::where('name', Role::ROLE_AUTHOR)->first();

        foreach ($users as $user) {
            $user->attachRole($role->id);
        }

        //create josef
        $josef = User::create([
            'name' => 'Josef',
            'email' => 'josef@publica.io',
            'surname' => 'Marc',
            'password' => bcrypt('publica'),
            'remember_token' => str_random(10),
            'wallet_password' => str_random(16),
        ]);

        $josef->attachRole($role->id);

        //create 1000 readers
        $users = factory(App\User::class, 1000)->create();
        $role = Role::where('name', Role::ROLE_READER)->first();

        foreach ($users as $user) {
            $user->attachRole($role->id);
        }
    }
}
