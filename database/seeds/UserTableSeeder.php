<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_admin =Role::where('name', 'Admin')->first();

        $user = new User();
        $user->first_name='yididiya';
        $user->last_name='kassahun';
        $user->email='yididiya1@gmail.com';
        $user->phone_number='0943342812';
        $user->fellowship_id='5';
        $user->password=bcrypt('1234');
        $user->save();
        $user->roles()->attach($role_admin); 
    }
}
