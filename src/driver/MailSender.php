<?php

namespace vakata\mail\driver;

class MailSender implements SenderInterface
{
    public function send(array $to, array $cc, array $bcc, $from, $subject, $headers, $message)
    {
        return @mail(implode(', ', $to), '=?utf-8?B?'.base64_encode((string) $subject).'?=', (string) $message, $headers) ?
            ['good' => array_merge($to, $cc, $bcc), 'fail' => []] :
            ['fail' => array_merge($to, $cc, $bcc), 'good' => []];
    }
}
