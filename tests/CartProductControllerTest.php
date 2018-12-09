<?php

namespace App\Tests;

class CartProductControllerTest extends BaseTestCase
{
    /**
     * @group cart_product
     */
    public function testAddCart()
    {
        $response = $this->request(
            'POST',
            '/carts',
            [
                'type' => 'cart'
            ]
        );

        $this->assertSame(201, $response->getStatusCode());

        $expected = [
            'type' => 'cart'
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart_product
     * @depends testAddCart
     */
    public function testAddCartItem($cart)
    {
        $response = $this->request(
            'POST',
            sprintf('/carts/%d/items', $cart['id']),
            [
                'type' => 'cart_item',
                'quantity' => 2,
                'product' => [
                    'id' => 2,
                ],
            ]
        );

        $this->assertSame(201, $response->getStatusCode());

        $expected = [
            'type' => 'cart_item',
            'quantity' => 2,
            'product' => [
                'id' => 2,
            ],
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart_product
     * @depends testAddCart
     */
    public function testGetCartItems($cart)
    {
        $response = $this->request(
            'GET',
            sprintf('/carts/%d/items', $cart['id'])
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            [
                'type' => 'cart_item',
                'quantity' => 2,
                'product' => [
                    'id' => 2,
                ],
            ],
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart_product
     * @depends testAddCart
     * @depends testAddCartItem
     */
    public function testEditCartItem($cart, $cartProduct)
    {
        $response = $this->request(
            'PUT',
            sprintf('/carts/%d/items/%d', $cart['id'], $cartProduct['id']),
            [
                'type' => 'cart_item',
                'quantity' => 5,
                'product' => [
                    'id' => 2,
                ],
            ]
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            'type' => 'cart_item',
            'quantity' => 5,
            'product' => [
                'id' => 2,
            ],
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart_product
     * @depends testAddCart
     * @depends testEditCartItem
     */
    public function testRemoveCartItem($cart, $cartProduct)
    {
        $response = $this->request(
            'DELETE',
            sprintf('/carts/%d/items/%d', $cart['id'], $cartProduct['id'])
        );

        $this->assertSame(204, $response->getStatusCode());
    }
}
