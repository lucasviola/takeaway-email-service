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
                        'Email' => $message->getAttributes()['from']['email'],
                        'Name' => $message->getAttributes()['from']['name']
                    ],
                    'To' => [
                        [
                            'Email' => $message->getAttributes()['to']['email'],
                            'Name' => $message->getAttributes()['to']['name']
                        ]
                    ],
                    'Subject' => $message->getAttributes()['subject'],
                    'TextPart' => $message->getAttributes()['message'],
                    'CustomID' => $message->getAttributes()['messageId']
                ]
            ]
        ];
        return $mailjetMessage;
    }

    public function mapMessageRequestToDomainModel(array $requestBodyAsJson, string $messageId): Message
    {
        $attributes = [
            'messageId' => $messageId,
            'from' => [
                'name' => $requestBodyAsJson['to']['name'],
                'email' => $requestBodyAsJson['to']['email'],
            ],
            'to' => [
                'name' => $requestBodyAsJson['from']['name'],
                'email' => $requestBodyAsJson['from']['email'],
            ],
            'subject' => $requestBodyAsJson['subject'],
            'message' => $requestBodyAsJson['message'],
        ];

        return new Message($attributes);
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
                            'email' => $message->getAttributes()['to']['email'],
                        ],
                    ],
                ],
            ],
            'from' => [
                'email' => $message->getAttributes()['from']['email'],
            ],
            'subject' => $message->getAttributes()['subject'],
            'content' => [
                0 => [
                    'type' => 'text/plain',
                    'value' => $message->getAttributes()['message'],
                ],
            ],
        ];
        return $sendgridMessage;
    }

    public function mapMessageToJson(Message $message): array
    {
        return [
            'from' => [
                'name' => $message->getAttributes()['from']['name'],
                'email' => $message->getAttributes()['from']['email'],
            ],
            'to' => [
                'name' => $message->getAttributes()['to']['name'],
                'email' => $message->getAttributes()['to']['email'],
            ],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message'],
        ];
    }

    public function mapMessageToMessageEntity(Message $message): array
    {
        return [
            'from' => $message->getAttributes()['from']['email'],
            'messageId' => $message->getAttributes()['messageId'],
            'to' => $message->getAttributes()['to']['email'],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message']
        ];
    }
}
