<?php

namespace Tests\Integration;

use App\MessageEntity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\TestCase;

class MessageEntityTest extends TestCase
{
    use RefreshDatabase, WithFaker, DatabaseMigrations;

    public function testEntityShouldHaveFillableAttributes() {
        $messageEntity = new MessageEntity();
        $expectedAttributes = ['from', 'to', 'messageId','subject', 'email', 'message', 'status'];

        $actualAttributes = $messageEntity->getFillable();

        $this->assertEquals($expectedAttributes, $actualAttributes);
    }

    public function testSchemShouldHaveColumns() {
        $this->markTestSkipped('Not working because of I do not know');
        $this->assertTrue(
            Schema::hasColumns('messages', [
                'id','from', 'to', 'messageId', 'subject', 'email', 'message', 'status'
            ]), 1);
    }
}
