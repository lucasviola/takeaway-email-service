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
                'email' => 'email@test.com',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email@test.com',
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
                'name' => 'name@gmail',
                'email' => 'email@gmail',
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
                'name' => 'email@test',
                'email' => 'email@test',
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
                'email' => 'email@test.com',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email@test.com',
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
                'email' => 'email@test.com',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email@test.com',
            ],
            'subject' => 'subject',
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }

    public function testShouldReturnTrueIfEmailIsNotInTheCorrectFormat() {
        $request = [
            'from' => [
                'name' => 'name',
                'email' => 'email@test.com',
            ],
            'to' => [
                'name' => 'name',
                'email' => 'email',
            ],
            'subject' => 'subject',
            'status' => 'status',
            'message' => 'message'
        ];
        $requestValidator = new MessageRequestValidator();

        $actual = $requestValidator->hasErrors($request);

        $this->assertTrue($actual);
    }
}
