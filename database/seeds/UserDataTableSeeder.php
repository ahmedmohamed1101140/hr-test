<?php

use Illuminate\Database\Seeder;
use App\Role;

class UserDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        foreach (Role::all() as $role) {
          DB::table('users_data')->insert([
              'user_id' => 1,
              'group_id' => $role->group_id,
              'resort_id' => $role->resort_id,
              'role_id' => $role->id
          ]);
        }
    }
}
