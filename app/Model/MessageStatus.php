<?php


namespace App\Model;


abstract class MessageStatus
{
    const SENT = 'SENT';
    const FAILED = 'FAILED';
    const POSTED = 'POSTED';
    const QUEUED = 'queued';
}
