<?php

namespace Tests\Integration;

use App\Adapter\MailjetEmailClient;
use App\Client\PostEmailService;
use App\Http\Controllers\Message\MessageController;
use App\Mapper\MessageMapper;
use App\Service\MessageService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    public function testSendMessageApiShouldRespondWith202AndMailjetResponse() {
        $mapper = new MessageMapper();
        $client = new MockHandler([
            new Response(200, ['content-type' => 'application/json'],
                $this->buildMailjetResponseBody()),
        ]);
        $handlerStack = HandlerStack::create($client);
        $mockHttpClient = new Client(['handler' => $handlerStack]);
        $client = new PostEmailService(new MailjetEmailClient($mapper, $mockHttpClient));
        $service = new MessageService($client);
        $controller = new MessageController($service, $mapper);
        $request = new Request([], [], [], [], [], [], $this->buildRequestBody());

        $actualResponse = $controller->send($request);

        $this->assertEquals(202, $actualResponse->getStatusCode());
        $this->assertEquals($this->buildResponse(), $actualResponse->getContent());
    }

    private function buildRequestBody() {
        $jsonAsString = '{
                          "from": {
                            "name": "name",
                            "email": "email"
                          },
                          "to": {
                            "name": "name",
                            "email": "email"
                          },
                          "subject": "subject",
                          "message": "message"
                        }';
        return $jsonAsString;
    }

    private function buildMailjetResponseBody(): string {
        return '{"Messages":[{"Status":"success","CustomID":"developmentTest","To":[{"Email":"lucasmatzenbacher@gmail.com","MessageUUID":"fa2f032e-299e-4541-9ec0-b83f86e673f2","MessageID":1152921511742440156,"MessageHref":"https://api.mailjet.com/v3/REST/message/1152921511742440156"}],"Cc":[],"Bcc":[]}]}';
    }

    private function buildResponse()
    {
        return '{"messageId":"1152921511742440156","status":"success"}';
    }
}
