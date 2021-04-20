<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MessageEntity;
use Faker\Generator as Faker;
use Illuminate\Support\Str;


$factory->define(MessageEntity::class, function (Faker $faker) {
    return [
        'from' => $faker->unique()->safeEmail,
        'to' => $faker->unique()->safeEmail,
        'messageStatus' => $faker->unique(),
        'messageId' => Str::random(10),
        'subject' => 'Your order is arriving',
        'message' => 'Your order is arriving.',
    ];
});
