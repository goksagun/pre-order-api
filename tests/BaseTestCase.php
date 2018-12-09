<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BaseTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * ApiTestCase constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = static::createClient();
    }

    /**
     * @param $method
     * @param $uri
     * @param array $parameters
     * @param array $headers
     * @return Response
     */
    public function request($method, $uri, $parameters = [], $headers = [])
    {
        $headers = array_merge(
            [
                'CONTENT_TYPE' => 'application/json',
                'PHP_AUTH_USER' => 'ryan',
                'PHP_AUTH_PW' => 'ryanpass',
            ],
            $headers
        );

        $content = null;
        if (in_array($method, ['POST', 'PUT'])) {
            $content = json_encode($parameters);
        }

        $this->client->request(
            $method,
            $uri,
            $parameters,
            [],
            $headers,
            $content
        );

        return $this->client->getResponse();
    }

    /**
     * @param Response $response
     * @return mixed
     */
    public function getArrayContent(Response $response)
    {
        return json_decode($response->getContent(), true);
    }

    /**
     * @param $expected
     * @param $actual
     * @param string $message
     */
    public function assertArraySame($expected, $actual, $message = '')
    {
        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                $this->assertArraySame($value, $actual[$key]);

                continue;
            }

            $this->assertSame($value, $actual[$key], $message);
        }
    }
}