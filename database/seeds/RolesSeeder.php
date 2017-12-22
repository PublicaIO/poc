<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Role();
        $admin->name         = 'admin';
        $admin->display_name = 'User Administrator';
        $admin->description  = 'User is allowed to manage and edit other users';
        $admin->save();

        $owner = new Role();
        $owner->name         = 'author';
        $owner->display_name = 'Author';
        $owner->description  = 'User is author and is allowed to publish new crowd sale';
        $owner->save();

        $owner = new Role();
        $owner->name         = 'reader';
        $owner->display_name = 'Reader';
        $owner->description  = 'User is reader';
        $owner->save();
    }
}
