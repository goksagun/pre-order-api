<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public const PRODUCT_REFERENCE_PREFIX = 'product';
    public const PRODUCT_NUM_ITEMS = 99;
    public const CART_REFERENCE_PREFIX = 'cart';
    public const CART_NUM_ITEMS = 9;
    public const CART_PRODUCT_REFERENCE_PREFIX = 'cart_product';
    public const CART_PRODUCT_NUM_ITEMS = 33;
    public const ORDER_REFERENCE_PREFIX = 'order';
    public const ORDER_NUM_ITEMS = 9;

    public function load(ObjectManager $manager)
    {
        $this->loadProducts($manager);
        $this->loadCarts($manager);
        $this->loadCartProducts($manager);
        $this->loadOrders($manager);
    }

    private function loadProducts(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= self::PRODUCT_NUM_ITEMS; ++$i) {
            $product = new Product();
            $product->setSku($faker->ean8);
            $product->setName(ucwords($faker->words($faker->numberBetween(1, 5), true)));
            $product->setPrice($faker->randomFloat(2, 9, 9999));
            $product->setStock($faker->numberBetween(1, 99));
            $product->setDescription($faker->boolean(70) ? $faker->text : null);

            $manager->persist($product);

            $this->addReference(sprintf('%s_%d', self::PRODUCT_REFERENCE_PREFIX, $i), $product);

            if ($i % 20 == 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    private function loadCarts(ObjectManager $manager)
    {
        for ($i = 1; $i <= self::CART_NUM_ITEMS; ++$i) {
            $cart = new Cart();
            $cart->setType(Cart::TYPE_CART);

            $manager->persist($cart);

            $this->addReference(sprintf('%s_%d', self::CART_REFERENCE_PREFIX, $i), $cart);

            if ($i % 20 == 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    private function loadCartProducts(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 1; $i <= self::CART_PRODUCT_NUM_ITEMS; ++$i) {
            $cartProduct = new CartProduct();
            /** @var Cart $cart */
            $cart = $this->getReference(
                sprintf('%s_%d', self::CART_REFERENCE_PREFIX, $faker->numberBetween(1, self::CART_NUM_ITEMS))
            );
            /** @var Product $product */
            $product = $this->getReference(
                sprintf('%s_%d', self::PRODUCT_REFERENCE_PREFIX, $faker->numberBetween(1, self::PRODUCT_NUM_ITEMS))
            );

            $cartProduct->setProduct($product);
            $cartProduct->setType(CartProduct::TYPE_CART_ITEM);
            $cartProduct->setQuantity($faker->numberBetween(1, 9));

            $cart->addItem($cartProduct);

            $manager->persist($cartProduct);

            $this->addReference(sprintf('%s_%d', self::CART_PRODUCT_REFERENCE_PREFIX, $i), $cartProduct);

            if ($i % 20 == 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }

    private function loadOrders(ObjectManager $manager)
    {
        $faker = Factory::create();

        $cartIndexes = [];
        for ($i = 1; $i <= self::ORDER_NUM_ITEMS; ++$i) {
            $order = new Order();

            randomIndex:
            $cartIndex = $faker->numberBetween(1, self::CART_NUM_ITEMS);
            if (in_array($cartIndex, $cartIndexes)) {
                goto randomIndex;
            }
            $cartIndexes[] = $cartIndex;

            /** @var Cart $cart */
            $cart = $this->getReference(
                sprintf('%s_%d', self::CART_REFERENCE_PREFIX, $cartIndex)
            );

            $order->setCart($cart);
            $order->setType(Order::TYPE_ORDER);
            $order->setEmail($faker->email);
            $order->setFirstName($faker->firstName);
            $order->setLastName($faker->lastName);
            $order->setPhone($faker->phoneNumber);

            foreach ($cart->getItems() as $cartProduct) {
                $orderProduct = new OrderProduct();
                $orderProduct->setSku($cartProduct->getProduct()->getSku());
                $orderProduct->setName($cartProduct->getProduct()->getName());
                $orderProduct->setStock($cartProduct->getProduct()->getStock());
                $orderProduct->setPrice($cartProduct->getProduct()->getPrice());
                $orderProduct->setQuantity($cartProduct->getQuantity());

                $order->addItem($orderProduct);
            }

            $manager->persist($order);

            $this->addReference(sprintf('%s_%d', self::ORDER_REFERENCE_PREFIX, $i), $order);

            if ($i % 20 == 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
    }
}
