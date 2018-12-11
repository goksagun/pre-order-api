<?php

namespace App\Tests;

class OrderControllerTest extends BaseTestCase
{
    /**
     * @group order
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
     * @group order
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
     * @group order
     * @depends testAddCart
     */
    public function testAddCheckoutReturnsUnprocessableError($cart)
    {
        $response = $this->request(
            'POST',
            sprintf('/carts/%d/checkout', $cart['id']),
            [
                'type' => 'order',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'customer@example.com',
                'phone' => '123456789',
            ]
        );

        $this->assertSame(422, $response->getStatusCode());

        $expected = [
            'errors' => [
                [
                    'code' => 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3',
                    'message' => 'This value should be a mobile phone.',
                    'path' => '[phone]',
                ]
            ]
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group order
     * @depends testAddCart
     */
    public function testAddCheckout($cart)
    {
        $response = $this->request(
            'POST',
            sprintf('/carts/%d/checkout', $cart['id']),
            [
                'type' => 'order',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'customer@example.com',
                'phone' => '5556667788',
            ]
        );

        $this->assertSame(201, $response->getStatusCode());

        $expected = [
            'type' => 'order',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'customer@example.com',
            'phone' => '5556667788',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group order
     */
    public function testGetOrdersReturnsAccessDeniedUserHasNoRoleAdmin()
    {
        $response = $this->request(
            'GET',
            '/orders'
        );

        $this->assertSame(403, $response->getStatusCode());
    }

    /**
     * @group order
     */
    public function testGetOrdersReturnsOkUserHasRoleAdmin()
    {
        $response = $this->request(
            'GET',
            '/orders',
            [],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'kitten',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());

        $actual = $this->getArrayContent($response);

        $this->assertNotEmpty($actual);

        return $actual;
    }

    /**
     * @group order
     * @depends testAddCheckout
     */
    public function testShowOrder($order)
    {
        $response = $this->request(
            'GET',
            sprintf('/orders/%d', $order['id'])
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            'id' => $order['id'],
            'type' => 'order',
            'email' => 'customer@example.com',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group order
     * @depends testShowOrder
     */
    public function testEditOrderReturnsUnprocessableError($order)
    {
        $response = $this->request(
            'PUT',
            sprintf('/orders/%d', $order['id']),
            [
                'type' => 'order',
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'email' => 'customer@example.com',
                'phone' => '1234567890',
                'status' => 'approved',
            ],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'kitten',
            ]
        );

        $this->assertSame(422, $response->getStatusCode());

        $expected = [
            'errors' => [
                [
                    'code' => 'de1e3db3-5ed4-4941-aae4-59f3667cc3a3',
                    'message' => 'This value should be a mobile phone.',
                    'path' => '[phone]',
                ]
            ]
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group order
     * @depends testShowOrder
     */
    public function testEditOrder($order)
    {
        $response = $this->request(
            'PUT',
            sprintf('/orders/%d', $order['id']),
            [
                'type' => 'order',
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'email' => 'customer@example.com',
                'phone' => '5556667799',
                'status' => 'approved',
            ],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'kitten',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            'id' => $order['id'],
            'type' => 'order',
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'customer@example.com',
            'phone' => '5556667799',
            'status' => 'approved',
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }

    /**
     * @group order
     * @depends testEditOrder
     */
    public function testEditOrderStatus($order)
    {
        $response = $this->request(
            'PATCH',
            sprintf('/orders/%d', $order['id']),
            [
                'status' => 'rejected',
            ],
            [
                'PHP_AUTH_USER' => 'admin',
                'PHP_AUTH_PW' => 'kitten',
            ]
        );

        $this->assertSame(200, $response->getStatusCode());

        $expected = [
            [
                'id' => $order['id'],
                'status' => 'rejected',
            ],
        ];
        $actual = $this->getArrayContent($response);

        $this->assertArraySame($expected, $actual);

        return $actual;
    }
}
