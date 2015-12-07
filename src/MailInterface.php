<?php

namespace vakata\mail;

interface MailInterface
{
    public function getTo($mailOnly = false);
    public function setTo($mail);
    public function getCc($mailOnly = false);
    public function setCc($mail);
    public function getBcc($mailOnly = false);
    public function setBcc($mail);
    public function getFrom();
    public function setFrom($mail);
    public function getSubject();
    public function setSubject($subject);
    public function getMessage();
    public function setMessage($message, $isHTML = true);
    public function isHTML();

    public function getHeaders();
    public function setHeader($header, $value);
    public function hasHeader($header);
    public function getHeader($header);
    public function removeHeader($header);
    public function removeHeaders();

    public function hasAttachments();
    public function addAttachment($content, $name);
    public function getAttachments();
    public function removeAttachments();

    public function sign($crt, $key, $pass = null, $ca = null);

    public function __toString();
}
