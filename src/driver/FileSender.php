<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;
use \vakata\mail\MailException;

/**
 * A mail helper class that stores emails on the disk instead of sending them - useful for debugging.
 */
class FileSender implements SenderInterface
{
    protected $dir;
    /**
     * Create an instance.
     * @param  string      $dir the path to save all emails to
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }
    /**
     * Send a message.
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'fail' - indicating successfull and failed addresses
     */
    public function send(MailInterface $mail)
    {
        $data = (string)$mail;
        if (!is_dir(realpath($this->dir))) {
            mkdir($this->dir, 0777, true);
            $this->dir = realpath($this->dir);
            if (!is_dir($this->dir)) {
                throw new MailException('Invalid mail dump dir');
            }
        }
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
