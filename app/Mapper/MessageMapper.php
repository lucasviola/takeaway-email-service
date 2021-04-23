<?php


namespace App\Mapper;


use App\MessageEntity;
use App\Client\Providers\EmailProviderResponse;
use App\Model\Message;
use App\Model\MessageSent;
use App\Model\MessageStatus;

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

    public function mapMessageRequestToDomainModel(array $requestBodyAsJson,
                                                   string $messageId,
                                                   string $status): Message
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
            'status' => $status
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

    public function mapMessageToMessageEntity(Message $message, MessageSent $messageSent): MessageEntity
    {
        $attributes = [
            'from' => $message->getAttributes()['from']['email'],
            'messageId' => $messageSent->getAttributes()['messageId'],
            'to' => $message->getAttributes()['to']['email'],
            'subject' => $message->getAttributes()['subject'],
            'message' => $message->getAttributes()['message'],
            'status' => $messageSent->getAttributes()['status'],
        ];

        return new MessageEntity($attributes);
    }

    public function mapFromSendgridResponseToMessageSent(EmailProviderResponse $sendGridResponse): MessageSent
    {
        $attributes = [
            'messageId' => uniqid(),
            'status' => Messagestatus::SENT
        ];

        $messageSent = new MessageSent($attributes);

        return $messageSent;
    }

    public function mapFromMailjetResponseToMessageSent(EmailProviderResponse $mailjetResponse): MessageSent
    {
        $attributes = [
            'messageId' => $mailjetResponse->getAttributes()['Messages'][0]['CustomID'],
            'status' => $mailjetResponse->getAttributes()['Messages'][0]['Status']
        ];

        $messageSent = new MessageSent($attributes);

        return $messageSent;
    }
}
