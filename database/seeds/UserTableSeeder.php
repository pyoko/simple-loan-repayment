<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        $password = bcrypt('secret');
        for ($i = 0; $i < 20; $i++) {
	        \App\User::create([
	        	'family_name' => $faker->lastName,
				'first_name'  => $faker->firstName,
				'email'       => $faker->unique()->safeEmail,
				'phone'       => $faker->e164PhoneNumber,
				'password'    => $password,
	        ]);
        }
    }
}
