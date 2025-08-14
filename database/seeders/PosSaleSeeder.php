<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PosSale;
use Faker\Factory as Faker;

class PosSaleSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            $serviceFee = $faker->randomFloat(2, 200, 1000);
            $discount = $faker->randomFloat(2, 0, 200);
            $total = $serviceFee - $discount;

            PosSale::create([
                'customer_name' => $faker->name,
                'service'       => $faker->randomElement([
                    'Vaccination',
                    'Checkup',
                    'Surgery',
                    'Grooming',
                    'Deworming'
                ]),
                'service_fee'   => $serviceFee,
                'discount'      => $discount,
                'total'         => $total > 0 ? $total : 0,
                'created_at'    => $faker->dateTimeBetween('-6 months', 'now'),
            ]);
        }
    }
}
