<?php

namespace TextHyphenation\Tests;


use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;
use PHPUnit\Framework\TestCase;

class PatternsControllerTest extends TestCase
{
    /**
     * @param string $url
     * @throws \Exception
     *
     * @dataProvider urlProvider
     */
    public function testGetAction(string $url): void
    {
        $client = new Client('http://127.0.0.1');
        $request = $client->get($url);

        $response = $request->send();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testPostAction(): void
    {
        $client = new Client('http://127.0.0.1');
        $data = [
            'pattern' => 'pattern1'
        ];

        $request = $client->post('/TextHyphenation/patterns', null, json_encode($data));

        $response = $request->send();
        $this->assertSame(200, $response->getStatusCode());

        $this->expectException(ClientErrorResponseException::class);
        $request->send();
    }

    public function testUpdateAction(): void
    {
        $client = new Client('http://127.0.0.1');
        $data = [
            'oldPattern' => 'pattern1',
            'newPattern' => 'pattern2',
        ];

        $request = $client->put('/TextHyphenation/patterns', null, json_encode($data));
        $response = $request->send();

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testDeletePattern(): void
    {
        $client = new Client('http://127.0.0.1');
        $data = [
            'pattern' => 'pattern2',
        ];

        $request = $client->delete('/TextHyphenation/patterns', null, json_encode($data));
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