<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;
use \vakata\mail\MailException;

/**
 * A mail sender class that sends mail through multiple other senders - useful for debugging.
 */
class MultiSender implements SenderInterface
{
    protected $senders = [];

    /**
     * Create an instance.
     * @param  array      $senders the sender instances to route through
     */
    public function __construct(array $senders = [])
    {
        foreach ($senders as $sender) {
            $this->addSender($sender);
        }
    }
    public function addSender(SenderInterface $sender)
    {
        $this->senders[] = $sender;
        return $this;
    }
    public function removeSender(SenderInterface $sender)
    {
        foreach ($this->senders as $k => $v) {
            if ($v === $sender) {
                unset($this->senders[$k]);
            }
        }
        return $this;
    }
    /**
     * Send a message.
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'fail' - indicating successfull and failed addresses
     */
    public function send(MailInterface $mail)
    {
        $all = array_merge(
            $mail->getTo(true),
            $mail->getCc(true),
            $mail->getBcc(true)
        );
        if (!count($this->senders)) {
            return [ 'good' => [], 'fail' => $all ];
        }
        $res = [ 'good' => [], 'fail' => [] ];
        foreach ($this->senders as $sender) {
            $tmp = $sender->send($mail);
            $res['good'] = array_unique(array_merge($res['good'], $tmp['good']));
            $res['fail'] = array_unique(array_merge($res['fail'], $tmp['fail']));
        }
        return $res;
    }
}
