<?php

namespace Tests\Client;

use App\Client\Client;
use App\Exceptions\ProviderNotAvailableException;
use App\Model\EmailProviderResponse;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    protected function setUp(): void
    {
        Log::spy();
    }

    public function testWhenApiReturnsOkShouldMapResponseToDomainMessage() {
        $client = new MockHandler([
            new \GuzzleHttp\Psr7\Response(200, ['content-type' => 'application/json'],
                '{"status":"success"}'),
        ]);
        $handlerStack = HandlerStack::create($client);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        $client =  new Client($httpClient);
        $expectedMailjetResponse =
            new EmailProviderResponse(['status'=> 'success']);

        $actualResponse = $client->post('http://fake.url', []);

        $this->assertEquals($actualResponse, $expectedMailjetResponse);
    }

    public function testShouldThrowEmailProviderNotAvailableWhenHttpExceptionOccurs() {
        $client = new MockHandler([
            new RequestException('Error Communicating with Server',
                new Request('POST', 'test'))
        ]);
        $handlerStack = HandlerStack::create($client);
        $httpClient = new \GuzzleHttp\Client(['handler' => $handlerStack]);
        $client =  new Client($httpClient);

        $this->expectException(ProviderNotAvailableException::class);

        $client->post('http://fake.url', []);
    }
}
