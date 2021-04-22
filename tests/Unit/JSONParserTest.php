<?php

namespace Tests\Unit;

use App\Utils\JSONParser;
use PHPUnit\Framework\TestCase;

class JSONParserTest extends TestCase
{
    public function testShouldParseJsonToString()
    {
        $json = $this->buildExampleJson();
        $expected = $this->buildExampleString();

        $actual = JSONParser::parseToString($json);

        $this->assertEquals($actual, $expected);
    }

    public function testShouldParseStringToJson()
    {
        $expected = $this->buildExampleJson();
        $string = $this->buildExampleString();

        $actual = JSONParser::parseToJson($string);

        $this->assertEquals($actual, $expected);
    }

    private function buildExampleJson(): array
    {
        return [
            'Messages' => [
                0 => [
                    'Status' => 'success',
                    'CustomID' => 'test',
                    'To' => [
                        0 => [
                            'Email' => 'email@gmail.com',
                            'MessageUUID' => 'uid',
                            'MessageID' => 1152921511802880648,
                            'MessageHref' => 'http://mailjet.href']],
                    'Cc' => [],
                    'Bcc' => []]]];
    }

    private function buildExampleString(): string {
        return '{"Messages":[{"Status":"success","CustomID":"test","To":[{"Email":"email@gmail.com","MessageUUID":"uid","MessageID":1152921511802880648,"MessageHref":"http:\/\/mailjet.href"}],"Cc":[],"Bcc":[]}]}';
    }
}
