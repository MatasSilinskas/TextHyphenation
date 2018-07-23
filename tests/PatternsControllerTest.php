<?php

namespace TextHyphenation\Tests;


use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use PHPUnit\Framework\TestCase;

class PatternsControllerTest extends TestCase
{
    /* @var Client */
    private $client;

    protected function setUp(): void
    {
        $this->client = new Client('http://127.0.0.1');
    }

    /**
     * @param string $url
     * @throws \Exception
     *
     * @dataProvider urlProvider
     */
    public function testGetAction(string $url): void
    {
        $request = $this->client->get($url);

        $response = $request->send();
        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testPostAction(): void
    {
        $this->client = new Client('http://127.0.0.1');
        $data = [
            'pattern' => 'pattern1'
        ];

        $request = $this->client->post('/TextHyphenation/patterns', null, json_encode($data));

        $response = $request->send();
        $this->assertSame(200, $response->getStatusCode());

        $this->expectException(ClientErrorResponseException::class);
        $request->send();
    }

    /**
     * @throws \Exception
     */
    public function testUpdateAction(): void
    {
        $this->client = new Client('http://127.0.0.1');
        $data = [
            'oldPattern' => 'pattern1',
            'newPattern' => 'pattern2',
        ];

        $request = $this->client->put('/TextHyphenation/patterns', null, json_encode($data));
        $response = $request->send();

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testDeletePattern(): void
    {
        $this->client = new Client('http://127.0.0.1');
        $data = [
            'pattern' => 'pattern2',
        ];

        $request = $this->client->delete('/TextHyphenation/patterns', null, json_encode($data));
        $response = $request->send();

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProvider(): array
    {
        return [
            ['/TextHyphenation/patterns'],
            ['/TextHyphenation/patterns/'],
        ];
    }
}