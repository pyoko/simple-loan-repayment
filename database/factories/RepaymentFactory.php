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

$factory->define(App\Repayment::class, function (Faker $faker) {
    return [
		'payment_amount' => $faker->numberBetween(1000, 100000),
		'balance'        => 0,
		'remarks'        => $faker->sentence(),
    ];
});
