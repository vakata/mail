<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;

/**
 * A mail sender class that sends message using the built-in PHP mail() function.
 */
class MailSender implements SenderInterface
{
    /**
     * Send a message.
     * @method send
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'bad' - indicating successfull and failed addresses
     */
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
