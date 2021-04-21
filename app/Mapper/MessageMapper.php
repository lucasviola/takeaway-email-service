<?php


namespace App\Mapper;


use App\Model\From;
use App\Model\Message;
use App\Model\MessageModel;
use App\Model\To;

class MessageMapper
{
    public function mapMessageToMailjetMessage(MessageModel $message): array {
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

    public function mapMessageRequestToDomainModel(array $requestBodyAsJson, string $messageId): MessageModel
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

        return new MessageModel($attributes);
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

    public function mapMessageToSendgridMessage(MessageModel $message): array {
        $sendgridMessage = [
            'personalizations' => [
                0 => [
                    'to' => [
                        0 => [
                            'email' => $message['email'],
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

    public function mapMessageToJson(MessageModel $message): array
    {
        return [
            'from' => [
                'name' => $message['from']['name'],
                'email' => $message['from']['email'],
            ],
            'to' => [
                'name' => $message['to']['name'],
                'email' => $message['to']['email'],
            ],
            'subject' => $message['subject'],
            'message' => $message['message'],
        ];
    }

    public function mapMessageToMessageEntity(MessageModel $message): array
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
