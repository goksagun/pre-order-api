<?php

namespace App\Tests;

class CartControllerTest extends BaseTestCase
{
    /**
     * @group cart
     */
    public function testAddCart()
    {
        $response = $this->request(
            'POST',
            '/carts',
            [
                'type' => 'cart',
            ]
        );

        $this->assertSame(201, $response->getStatusCode());

        $expected = [
            'type' => 'cart',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart
     */
    public function testGetCarts()
    {
        $response = $this->request(
            'GET',
            '/carts'
        );

        $this->assertSame(200, $response->getStatusCode());

        $actual = $this->getArrayContent($response);

        $this->assertNotEmpty($actual);
    }

    /**
     * @group cart
     * @depends testAddCart
     */
    public function testShowCart($cart)
    {
        $response = $this->request(
            'GET',
            sprintf('/carts/%d', $cart['id'])
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            'type' => 'cart',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart
     * @depends testShowCart
     */
    public function testEditCart($cart)
    {
        $response = $this->request(
            'PUT',
            sprintf('/carts/%d', $cart['id']),
            [
                'type' => 'cart',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            'type' => 'cart',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group cart
     * @depends testEditCart
     */
    public function testRemoveCart($cart)
    {
        $response = $this->request(
            'DELETE',
            sprintf('/carts/%d', $cart['id'])
        );

        $this->assertSame(204, $response->getStatusCode());
    }
}
