<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
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
		DB::statement("TRUNCATE roles;");
		DB::table('roles')->delete();
    	DB::table('roles')->insert(

		[
            array(
                "id" => 1,
                "name" => "admin",
                "display_name" => "Admin",
                "description" => "administrator system",
                "created_at" => "2018-10-15 08:19:29",
                "updated_at" => "2019-04-05 08:44:11",
            ),
            array(
                "id" => 2,
                "name" => "user",
                "display_name" => "user",
                "description" => "Level User Input",
                "created_at" => "2019-03-15 03:41:53",
                "updated_at" => "2019-03-25 15:45:15",
            ),
            array(
                "id" => 3,
                "name" => "budgeting",
                "display_name" => "Budgeting",
                "description" => "Bertugas sebagai upload MBG dan RBG, validasi budget, open and close budget",
                "created_at" => "2019-03-25 08:17:17",
                "updated_at" => "2019-03-25 08:17:17",
            ),
            array(
                "id" => 4,
                "name" => "department-head",
                "display_name" => "Department Head",
                "description" => "Level Department Head",
                "created_at" => "2019-03-28 08:09:48",
                "updated_at" => "2019-03-28 08:09:48",
            ),
            array(
                "id" => 5,
                "name" => "gm",
                "display_name" => "GM",
                "description" => "Level Group Manager",
                "created_at" => "2019-03-28 08:11:55",
                "updated_at" => "2019-03-28 08:11:55",
            ),
            array(
                "id" => 6,
                "name" => "director",
                "display_name" => "Director",
                "description" => "Level Director",
                "created_at" => "2019-03-28 08:13:30",
                "updated_at" => "2019-03-28 08:13:30",
            ),
            array(
                "id" => 7,
                "name" => "purchasing",
                "display_name" => "Purchasing",
                "description" => "Purchasing controller, Level yang berfungsi untuk administrasi dan pricing di approval sheet",
                "created_at" => "2019-03-28 09:02:45",
                "updated_at" => "2019-03-28 09:02:45",
            ),
            array(
                "id" => 8,
                "name" => "accounting",
                "display_name" => "Accounting",
                "description" => "Level yang berfungsi untuk register fixed asset",
                "created_at" => "2019-03-28 09:06:33",
                "updated_at" => "2019-03-28 09:06:33",
            ),
        ]);

            // Activate FOREIGN_KEY_CHECKS again..
            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
    }
}
