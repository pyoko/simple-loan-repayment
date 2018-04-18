<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Loan::class, function (Faker $faker) {
    return [
		'amount'          => $faker->numberBetween(1000, 100000),
		'arrangement_fee' => $faker->numberBetween(100, 1000),
		'interest_rate'   => $faker->randomFloat(2, 5, 12),
		'term'            => $faker->randomElement(['12', '24']),
		'frequency'       => $faker->randomElement(['weekly', 'fortnightly']),
    ];
});
