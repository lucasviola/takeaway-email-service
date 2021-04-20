<?php

namespace App\Model;

class Message
{
    private From $from;
    private To $to;
    private string $messageId;
    private String $subject;
    private String $message;

    public function __construct(string $messageId, From $from, To $to, String $subject, String $message)
    {
        $this->messageId = $messageId;
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function getMessageId(): string
    {
        return $this->messageId;
    }

    public function getFrom(): From
    {
        return $this->from;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getTo(): To
    {
        return $this->to;
    }
}
