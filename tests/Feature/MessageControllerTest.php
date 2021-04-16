<?php

namespace Tests\Feature;

use Tests\TestCase;

class MessageControllerTest extends TestCase
{

    public function testShouldReceive202WithJsonResponseBody()
    {
        $actualResponse = $this->postJson('/api/message', $this->buildRequestBody(), []);

        $actualResponse->assertStatus(202);
        $actualResponse->assertJson($this->buildResponseBody());
    }

    private function buildRequestBody() {
        $requestAsJson = '{}';
        return json_decode($requestAsJson, true);
    }

    private function buildResponseBody() {
        $expectedResponse = '{
                                "messageId": "abCdfG",
                                "messageStatus": "Queued"
                             }';
        return json_decode($expectedResponse, true);
    }
}
