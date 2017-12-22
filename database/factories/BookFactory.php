<?php

use Faker\Generator as Faker;

$factory->define(App\Book::class, function (Faker $faker) {
    static $status;

    return [
        'title'                 => $title = $faker->sentence(random_int(1, 7)),
        'url'                   => str_slug($title),
        'promotion_text'        => $faker->sentence(random_int(25, 50)),
        'goal'                  => rand(1000, 100000),
        'price_for_crowdsale'   => rand(1, 10),
        'soft_cap'              => rand(1000, 100000),
        'duration'              => rand(1, 60),
        'price_after_crowdsale' => rand(1000, 100000),
        'aftersale_keys_amount' => rand(100000, 1000000),
        'status'                => $status ?? rand(1, 6),
        'short_description'     => $faker->sentence(random_int(50, 100)),
        'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
    ];
});
