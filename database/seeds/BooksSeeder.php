<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Book;
use App\Role;
use Faker\Factory as Faker;


class BooksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        //create books for authors
        $authors = User::withRole(Role::ROLE_AUTHOR)->where('email', '!=', 'josef@publica.io')->get();

        foreach ($authors as $user) {
            $user->books()->saveMany(factory(App\Book::class, 20)->make());
        }

        $josef = User::where('email', 'josef@publica.io')->first();

        if ($josef) {
            $josef->books()->createMany([
                [
                    'title'                 => 'Blockchain: Design Sprint.',
                    'url'                   => str_slug('Blockchain: Design Sprint.'),
                    'promotion_text'        => $faker->sentence(random_int(25, 50)),
                    'goal'                  => 10000,
                    'price_for_crowdsale'   => 3,
                    'soft_cap'              => 3000,
                    'duration'              => 45,
                    'price_after_crowdsale' => 9,
                    'aftersale_keys_amount' => 100000,
                    'status'                => 1,
                    'short_description'     => $faker->sentence(random_int(50, 100)),
                    'cover_art'             => 'images/samples/sample1.jpg',
                    'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
                ],
                [
                    'title'                 => 'Building Blockchain Projects',
                    'url'                   => str_slug('Building Blockchain Projects'),
                    'promotion_text'        => $faker->sentence(random_int(25, 50)),
                    'goal'                  => 20000,
                    'price_for_crowdsale'   => 5,
                    'soft_cap'              => 10000,
                    'duration'              => 45,
                    'price_after_crowdsale' => 20,
                    'aftersale_keys_amount' => 100000,
                    'status'                => 2,
                    'short_description'     => $faker->sentence(random_int(50, 100)),
                    'cover_art'             => 'images/samples/sample2.jpg',
                    'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
                ],
                [
                    'title'                 => 'Blockchain for Dummies',
                    'url'                   => str_slug('Blockchain for Dummies'),
                    'promotion_text'        => $faker->sentence(random_int(25, 50)),
                    'goal'                  => 100000,
                    'price_for_crowdsale'   => 8,
                    'soft_cap'              => 15000,
                    'duration'              => 30,
                    'price_after_crowdsale' => 4,
                    'aftersale_keys_amount' => 100000,
                    'status'                => 3,
                    'short_description'     => $faker->sentence(random_int(50, 100)),
                    'cover_art'             => 'images/samples/sample3.jpg',
                    'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
                ],
                [
                    'title'                 => 'Blockchain Basics.',
                    'url'                   => str_slug('Blockchain Basics.'),
                    'promotion_text'        => $faker->sentence(random_int(25, 50)),
                    'goal'                  => 10000,
                    'price_for_crowdsale'   => 3,
                    'soft_cap'              => 7000,
                    'duration'              => 30,
                    'price_after_crowdsale' => 5,
                    'aftersale_keys_amount' => 100000,
                    'status'                => 4,
                    'short_description'     => $faker->sentence(random_int(50, 100)),
                    'cover_art'             => 'images/samples/sample4.jpg',
                    'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
                ],
                [
                    'title'                 => 'Mastering Bitcoin.',
                    'url'                   => str_slug('Mastering Bitcoin.'),
                    'promotion_text'        => $faker->sentence(random_int(25, 50)),
                    'goal'                  => 10000,
                    'price_for_crowdsale'   => 10,
                    'soft_cap'              => 5000,
                    'duration'              => 30,
                    'price_after_crowdsale' => 30,
                    'aftersale_keys_amount' => 100000,
                    'status'                => 5,
                    'short_description'     => $faker->sentence(random_int(50, 100)),
                    'cover_art'             => 'images/samples/sample5.jpg',
                    'crowdsale_start_date'  => $faker->dateTimeBetween('now', '+2 months'),
                ]
            ]);
        }

        //create book purchases
        $readers = User::withRole(Role::ROLE_READER)->get();
        $books = Book::whereIn('status', [Book::STATUS_CROWDSALE_STARTED, Book::STATUS_CROWDSALE_ENDED, Book::STATUS_PUBLISHED])->get();

        foreach ($books as $book) {
            foreach ($readers as $reader) {
                if (rand(0, 1) == 1) {
                    $reader->purchasedBooks()->save($book,
                        [
                            'transaction_hash' => str_random(64),
                            'amount'           => rand(1, 10),
                            'pbl_amount'       => $book->price_for_crowdsale / 0.2
                        ]
                    );
                }
            }
        }
    }
}
