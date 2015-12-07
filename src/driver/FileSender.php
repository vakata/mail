<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;
use \vakata\mail\MailException;

class FileSender implements SenderInterface
{
    public function __construct($dir)
    {
        $this->dir = realpath($dir);
        if (!$this->dir) {
            throw new MailException('Invalid mail dump dir');
        }
    }
    public function send(MailInterface $mail)
    {
        $data = (string)$mail;
        file_put_contents($this->dir . DIRECTORY_SEPARATOR . time() . '_' . md5($data) . '.txt', $data);
        return [
            'good' => array_merge(
                $mail->getTo(true),
                $mail->getCc(true),
                $mail->getBcc(true)
            ),
            'fail' => []
        ];
    }
}
