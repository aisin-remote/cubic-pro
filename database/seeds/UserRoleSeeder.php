<?php

use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DISABLE FOREIGN_KEY_CHECKS & TRUNCATE TABLE..
		DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
		DB::statement("TRUNCATE role_user;");
		DB::table('role_user')->delete();
    	DB::table('role_user')->insert(

		[
            array(
                "user_id" => 1,
                "role_id" => 1,
            ),
            array(
                "user_id" => 2,
                "role_id" => 2,
            ),
            array(
                "user_id" => 3,
                "role_id" => 2,
            ),
            array(
                "user_id" => 7,
                "role_id" => 3,
            ),
            array(
                "user_id" => 8,
                "role_id" => 4,
            ),
            array(
                "user_id" => 9,
                "role_id" => 5,
            ),
            array(
                "user_id" => 10,
                "role_id" => 6,
            ),
            array(
                "user_id" => 11,
                "role_id" => 7,
            ),
            array(
                "user_id" => 12,
                "role_id" => 8,
            ),
        ]);
        
            // Activate FOREIGN_KEY_CHECKS again..
            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
