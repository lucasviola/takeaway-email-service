<?php

namespace Tests\Unit;

use App\Http\MessageRequestValidator;
use Tests\TestCase;

class MessageRequestValidatorTest extends TestCase
{
    public function testShouldReturnFalseIfValidationPasses() {
        $request = [
            'from' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'message' => 'message'
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertFalse($actual);
    }

    public function testShouldReturnTrueIfFromFieldIsMissing() {
        $request = [
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'message' => 'message',
        ];

        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }

    public function testShouldReturnTrueIfToIsMissing() {
        $request = [
            'from' => [
                'name' => 'email',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'message' => 'message',
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }

    public function testShouldReturnTrueIfSubjectIsMissing() {
        $request = [
            'from' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'message' => 'message',
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }

    public function testShouldReturnTrueIfMessageIsMissing() {
        $request = [
            'from' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'status' => 'status'
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }
}
