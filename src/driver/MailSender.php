<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;

class MailSender implements SenderInterface
{
    public function send(MailInterface $mail)
    {
        $all = array_merge(
            $mail->getTo(true),
            $mail->getCc(true),
            $mail->getBcc(true)
        );
        list($headers, $message) = explode("\r\n\r\n", (string)$mail, 2);
        return @mail(
            implode(', ', $mail->getTo(true)),
            '=?utf-8?B?'.base64_encode((string) $mail->getSubject()).'?=',
            $message,
            $headers
        ) ? [ 'good' => $all, 'fail' => [] ] : [ 'fail' => $all, 'good' => [] ];
    }
}
