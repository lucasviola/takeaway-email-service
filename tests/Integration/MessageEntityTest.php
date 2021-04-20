<?php

namespace Tests\Integration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;

class MessageEntityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testMessageHasExpectedColumns() {
        $this->assertTrue(
            Schema::hasColumns('messages', [
                'id','messageId', 'from', 'to', 'subject', 'message'
            ]));
    }
}
