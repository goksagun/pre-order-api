<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public const NUM_ITEMS = 99;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::NUM_ITEMS; ++$i) {
            $product = new Product();
            $product->setSku($faker->ean8);
            $product->setName(ucwords($faker->words($faker->numberBetween(1, 5), true)));
            $product->setPrice($faker->randomFloat(2, 9, 9999));
            $product->setStock($faker->numberBetween(1, 99));
            $product->setDescription($faker->boolean(70) ? $faker->text : null);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
