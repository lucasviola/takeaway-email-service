<?php


namespace App\Model;


abstract class MessageStatus
{
    const SENT = 'SENT';
    const FAILED = 'failed';
    const QUEUED = 'queued';
}
