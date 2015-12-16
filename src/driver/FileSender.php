<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;
use \vakata\mail\MailException;

/**
 * A mail helper class that stores emails on the disk instead of sending them - useful for debugging.
 */
class FileSender implements SenderInterface
{
    /**
     * Create an instance.
     * @method __construct
     * @param  string      $dir the path to save all emails to
     */
    public function __construct($dir)
    {
        $this->dir = realpath($dir);
        if (!$this->dir) {
            throw new MailException('Invalid mail dump dir');
        }
    }
    /**
     * Send a message.
     * @method send
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'bad' - indicating successfull and failed addresses
     */
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
