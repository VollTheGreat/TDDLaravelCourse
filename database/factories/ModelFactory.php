<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Concert;
use Carbon\Carbon;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
$factory->define(App\Concert::class, function (Faker\Generator $faker) {

    return [
        'title' => 'Example Band',
        'subtitle'=> 'with The Fake Openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price'=> 2000,
        'venue'=> 'Example Theater',
        'venue_address'=> '123 example lane',
        'city'=> 'Fakeville',
        'state'=> 'on',
        'zip'=> '51-165',
        'additional_information' => 'some sample additional information',
    ];
});
$factory->state(Concert::class,'published',function ($faker){
   return [
      'published_at' => Carbon::parse('-1 week')
   ] ;
});
$factory->state(Concert::class,'unpublished',function ($faker){
   return [
      'published_at' => null,
   ] ;
});
$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    return [
        'concert_id' => function () {
            return factory(App\Concert::class)->create()->id;
        },
    ];
});
$factory->state(App\Ticket::class, 'reserved', function ($faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
$factory->define(App\Order::class, function (Faker\Generator $faker) {
    return [
        'amount' => 5250,
        'email' => 'somebody@example.com',
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'card_last_four' => '1234',
    ];
});