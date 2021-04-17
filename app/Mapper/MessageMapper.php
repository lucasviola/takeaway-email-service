<?php


namespace App\Mapper;


use App\Model\From;
use App\Model\Message;
use App\Model\To;

class MessageMapper
{

    public function __construct()
    {

    }

    public function mapToMailjetMessage(Message $message): array {
        $mailjetMessage = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $message->getFrom()->getEmail(),
                        'Name' => $message->getFrom()->getName()
                    ],
                    'To' => [
                        [
                            'Email' => $message->getTo()->getEmail(),
                            'Name' => $message->getTo()->getName()
                        ]
                    ],
                    'Subject' => $message->getSubject(),
                    'TextPart' => $message->getMessage(),
                    'CustomID' => "developmentTest"
                ]
            ]
        ];
        return $mailjetMessage;
    }

    public function mapToDomainModel(array $requestBodyAsJson): Message
    {
        $to = new To($requestBodyAsJson['to']['name'], $requestBodyAsJson['to']['email']);
        $from = new From($requestBodyAsJson['from']['name'], $requestBodyAsJson['from']['email']);
        $message = new Message($from, $to, $requestBodyAsJson['subject'], $requestBodyAsJson['message']);

        return $message;
    }
}
