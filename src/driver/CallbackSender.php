<?php

namespace vakata\mail\driver;

use Closure;
use \vakata\mail\MailInterface;
use \vakata\mail\MailException;

class CallbackSender implements SenderInterface
{
    protected Closure $sender;
    /**
     * Create an instance.
     * @param  string      $dir the path to save all emails to
     */
    public function __construct(callable $sender)
    {
        $this->sender = Closure::fromCallable($sender);
    }
    /**
     * Send a message.
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'fail' - indicating successfull and failed addresses
     */
    public function send(MailInterface $mail)
    {
        return $this->sender->call($this, $mail);
    }
}
