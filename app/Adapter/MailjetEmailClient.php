<?php


namespace App\Adapter;


use App\Mapper\MessageMapper;
use App\Model\Message;

class MailjetEmailClient
{
    private $messageMapper;
    private $url;

    public function __construct(MessageMapper $messageMapper)
    {
        $this->url = env('MAILJET_MESSAGE_URL');
        $this->messageMapper = $messageMapper;
    }

    public function getUrl()
    {
        return $this->url;
    }
    public function buildRequestOptions(Message $message): array {
        return [
            'auth' => [
                env('MAILJET_PUBLIC_KEY'),
                env('MAILJET_PRIVATE_KEY')
            ],
            'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'body' => json_encode($this->messageMapper->mapToMailjetMessage($message)),
            'debug' => false
        ];
    }
}
