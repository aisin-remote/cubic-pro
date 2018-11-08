<?php

use Illuminate\Database\Seeder;
use App\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->email= 'syechrugotama@gmail.com';
        $user->password= bcrypt('syechru356');

        $user->save();
    }
}
