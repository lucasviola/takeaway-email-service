<?php


namespace App\Mapper;


use App\Model\From;
use App\Model\Message;
use App\Model\To;

class MessageMapper
{
    public function mapMessageToMailjetMessage(Message $message): array {
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
                    'CustomID' => $message->getMessageId()
                ]
            ]
        ];
        return $mailjetMessage;
    }

    public function mapMessageRequestToDomainModel(array $requestBodyAsJson, string $messageId): Message
    {
        $to = new To($requestBodyAsJson['to']['name'], $requestBodyAsJson['to']['email']);
        $from = new From($requestBodyAsJson['from']['name'], $requestBodyAsJson['from']['email']);
        $message = new Message($messageId, $from, $to, $requestBodyAsJson['subject'], $requestBodyAsJson['message']);

        return $message;
    }

    public function mapMailjetResponseToMessageResponse(array $externalServiceResponse): array
    {
        $status = $externalServiceResponse['Messages'][0]['Status'];
        $messageId = $externalServiceResponse['Messages'][0]['To'][0]['MessageID'];
        $responseBody = ['messageId' => "$messageId", 'status' => $status];

        return $responseBody;
    }

    public function mapSendgridResponseToMessageResponse(): array
    {
        $status = 'success';
        $messageId = 'sendgrid';
        $responseBody = ['messageId' => "$messageId", 'status' => $status];

        return $responseBody;
    }

    public function mapMessageToSendgridMessage(Message $message): array {
        $sendgridMessage = [
            'personalizations' => [
                0 => [
                    'to' => [
                        0 => [
                            'email' => $message->getTo()->getEmail(),
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $message->getFrom()->getEmail(),
            ],
            'subject' => $message->getSubject(),
            'content' => [
                0 => [
                    'type' => 'text/plain',
                    'value' => $message->getMessage(),
                ],
            ],
        ];
        return $sendgridMessage;
    }

    public function mapMessageToJson(Message $message): array
    {
        return [
            'from' => [
                'name' => $message->getFrom()->getName(),
                'email' => $message->getFrom()->getEmail(),
            ],
            'to' => [
                'name' => $message->getTo()->getName(),
                'email' => $message->getTo()->getEmail(),
            ],
            'subject' => $message->getSubject(),
            'message' => $message->getMessage(),
        ];
    }

    public function mapMessageToMessageEntity(Message $message): array
    {
        return [
            'from' => $message->getFrom()->getEmail(),
            'messageId' => $message->getMessageId(),
            'to' => $message->getTo()->getEmail(),
            'subject' => $message->getSubject(),
            'message' => $message->getMessage()
        ];
    }
}
