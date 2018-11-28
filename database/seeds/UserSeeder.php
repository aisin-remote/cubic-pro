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
        $user->name= 'syechru';
        $user->status= 1;
        $user->sap_cc_code= '1';
        $user->email= 'syechrugotama@gmail.com';
        $user->password= bcrypt('syechru356');

        $user->save();
    }
}
