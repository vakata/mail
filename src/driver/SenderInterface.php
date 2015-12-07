<?php

namespace vakata\mail\driver;

interface SenderInterface
{
    public function send(array $to, array $cc, array $bcc, $from, $subject, $headers, $message);
}
