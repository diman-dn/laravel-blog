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
// Создание fake записей на примере постов, в кол-ве 10 шт:
// $: php artisan tinker
// factory(App\Post::class, 10)->create();

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
    ];
});

// Fake posts
$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'content' => $faker->sentence,
        'image' => 'no-image.png', // secret
        'date' => '01/11/18',
        'views' => $faker->numberBetween(0, 5000),
        'category_id' => $faker->numberBetween(1, 5),
//        'tags' => [1, 2, 3],
        'user_id' => 1,
        'status' => 1,
        'is_featured' => 0
    ];
});

// Fake categories
//$factory->define(App\Category::class, function (Faker $faker) {
//    return [
//        'title' => $faker->word,
//    ];
//});

// Fake tags
//$factory->define(App\Tag::class, function (Faker $faker) {
//    return [
//        'title' => $faker->word,
//    ];
//});