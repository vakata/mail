<?php

namespace vakata\mail\driver;

class FileSender implements SenderInterface
{
    public function __construct($dir)
    {
        $this->dir = realpath($dir);
        if (!$this->dir) {
            throw new \vakata\mail\MailException('Invalid mail dump dir');
        }
    }
    public function send(array $to, array $cc, array $bcc, $from, $subject, $headers, $message)
    {
        file_put_contents($this->dir.DIRECTORY_SEPARATOR.time().'_'.md5($headers.$message).'_'.rand(10, 99).'.txt', $headers."\r\n\r\n".$message);

        return ['good' => array_merge($to, $cc, $bcc), 'fail' => []];
    }
}
