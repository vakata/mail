<?php

namespace vakata\mail\driver;

use \vakata\mail\MailInterface;

interface SenderInterface
{
    public function send(MailInterface $message);
}
