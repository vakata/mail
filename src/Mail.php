<?php

namespace vakata\mail;

class Mail
{
    protected $from = null;
    protected $html = true;
    protected $subject = null;
    protected $message = null;
    protected $headers = [];
    protected $attached = [];
    protected $crt = null;
    protected $key = null;
    protected $pass = null;
    protected $ca = null;

    public function __construct($from = null, $subject = null, $message = null)
    {
        $this->subject = $subject;
        $this->message = $message;

        if ($from) {
            $this->setFrom($from);
        }
    }

    protected function cleanHeaderName($name)
    {
        if (strncmp($name, 'HTTP_', 5) === 0) {
            $name = substr($name, 5);
        }
        $name = str_replace('_', ' ', strtolower($name));
        $name = str_replace('-', ' ', strtolower($name));
        $name = str_replace(' ', '-', ucwords($name));

        return $name;
    }
    protected function getAddress($mail)
    {
        $mail = trim($mail);
        $mail = preg_replace(['(^\<)', '(\>$)'], '', $mail);
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return $mail;
        }
        if (!strpos($mail, '<')) {
            return;
        }
        $mail = explode('>', explode('<', $mail, 2)[1], 2)[0];
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return $mail;
        }

        return;
    }
    protected function getAddressString($mail)
    {
        $mail = trim($mail);
        $mail = preg_replace(['(^\<)', '(\>$)'], '', $mail);
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return $mail;
        }
        if (!strpos($mail, '<')) {
            return;
        }
        list($name, $mail) = explode('<', $mail, 2);
        $name = trim($name);
        $name = strpos($name, '=?') === 0 ? $name : '=?utf-8?B?'.base64_encode($name).'?=';
        $mail = explode('>', trim($mail), 2)[0];
        if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            return $name.' <'.$mail.'>';
        }

        return;
    }

    public function getFrom($mail)
    {
        return $this->message;
    }
    public function setFrom($mail)
    {
        $this->from = $this->getAddress($mail);
        if ($this->from) {
            $this->setHeader('From', $this->getAddressString($mail));
        }

        return $this;
    }
    public function getSubject()
    {
        return $this->subject;
    }
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function setMessage($message, $isHTML = true)
    {
        $this->message = $message;
        $this->html = $isHTML;

        return $this;
    }
    public function isHTML()
    {
        return $this->html;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
    public function setHeader($header, $value)
    {
        $this->headers[$this->cleanHeaderName($header)] = $value;

        return $this;
    }
    public function hasHeader($header)
    {
        return isset($this->headers[$this->cleanHeaderName($header)]);
    }
    public function getHeader($header)
    {
        return isset($this->headers[$this->cleanHeaderName($header)]) ? $this->headers[$this->cleanHeaderName($header)] : null;
    }
    public function removeHeader($header)
    {
        unset($this->headers[$this->cleanHeaderName($header)]);

        return $this;
    }
    public function removeHeaders()
    {
        $this->headers = [];

        return $this;
    }

    public function hasAttachments()
    {
        return count($this->attached);
    }
    public function addAttachment(\vakata\file\FileInterface $file, $name = null)
    {
        $this->attached[] = [$file, $name];

        return $this;
    }
    public function getAttachments()
    {
        return $this->attached;
    }
    public function removeAttachments()
    {
        $this->attached = [];

        return $this;
    }

    public function sign($crt, $key, $pass = null, $ca = null)
    {
        $this->crt = $crt ? realpath($crt) : null;
        $this->key = $key ? realpath($key) : null;
        $this->pass = $pass;
        $this->ca = $ca ? realpath($ca) : null;

        return $this;
    }

    public function send($to, $cc = null, $bcc = null, \vakata\mail\send\SenderInterface $sender = null)
    {
        $t = [];
        $c = [];
        $b = [];

        if (!is_array($to)) {
            $to = explode(',', $to);
        }
        foreach ($to as $k => $m) {
            $t[$k] = $this->getAddress($m);
            $to[$k] = $this->getAddressString($m);
        }
        $t = array_filter($t);
        $to = array_filter($to);

        if (!is_array($cc)) {
            $cc = explode(',', (string) $cc);
        }
        foreach ($cc as $k => $m) {
            $c[$k] = $this->getAddress($m);
            $cc[$k] = $this->getAddressString($m);
        }
        $c = array_filter($c);
        $cc = array_filter($cc);

        if (!is_array($bcc)) {
            $bcc = explode(',', (string) $bcc);
        }
        foreach ($bcc as $k => $m) {
            $b[$k] = $this->getAddress($m);
            $bcc[$k] = $this->getAddressString($m);
        }
        $b = array_filter($b);
        $bcc = array_filter($bcc);

        if (!count($t) && !count($c) && !count($b)) {
            throw new MailException('No valid mail to send to');
        }

        $message = str_replace(array("\r\n", "\r"), "\n", (string) $this->message);
        $message = explode("\n", $message);
        $length = 76;
        $result = '';
        foreach ($message as $row) {
            if (strlen($row) < $length) {
                $result .= $row."\r\n";
                continue;
            }
            $cnt = 0;
            $row = explode(' ', $row);
            foreach ($row as $k => $wrd) {
                if ($cnt > 0 && $cnt + strlen($wrd) + ($k == count($row) - 1 ? 0 : 1) > $length) {
                    $result .= "\r\n";
                    $cnt = 0;
                }
                $result .= $wrd.($k == count($row) - 1 ? '' : ' ');
                $cnt += strlen($wrd);
            }
            $result = rtrim($result);
            $result .= "\r\n";
        }

        $result_bnd = '==Alternative_Boundary_x'.md5(microtime()).'x';
        if ($this->html) {
            $alternative = '';
            $alternative .= '--'.$result_bnd."\r\n";
            $alternative .= 'Content-Type: text/plain; charset="utf-8"'."\r\n";
            $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
            $alternative .= strip_tags($result)."\r\n\r\n";
            $alternative .= '--'.$result_bnd."\r\n";
            if (strpos($result, '<img ') !== false) {
                $related_bnd = '==Related_Boundary_x'.md5(microtime()).'x';
                $alternative .= 'Content-Type: multipart/related; '."\r\n\t".'boundary="'.$related_bnd.'"'."\r\n\r\n";
                $alternative .= '--'.$related_bnd."\r\n";
                $alternative .= 'Content-Type: text/html; charset="utf-8"'."\r\n";
                $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
                $alternative .= preg_replace_callback(['(\<img(.*?)src\s*=\s*"([^"]+)")i', '(\<img(.*?)src\s*=\s*\'([^\']+)\')i', '(\<img(.*?)src=([^\'" ]+))i'], function ($matches) use (&$images) {
                    $k = md5($matches[2]).'@local.dev';
                    $images[$k] = $matches[2];

                    return '<img '.$matches[1].' src="cid:'.$k.'" ';
                }, $result);
                $alternative .= "\r\n\r\n";
                foreach ($images as $k => $image) {
                    $content = file_get_contents($image);

                    $fnfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = @finfo_buffer($fnfo, $content);
                    $extn = basename($image);
                    $extn = substr($extn, strrpos($extn, '.') + 1);
                    if (!$mime) {
                        continue;
                    }
                    $alternative .= '--'.$related_bnd."\r\n";
                    $alternative .= 'Content-Type: '.$mime.'; name="'.md5($k).'.'.$extn.'"'."\r\n";
                    $alternative .= 'Content-Transfer-Encoding: base64'."\r\n";
                    $alternative .= 'Content-ID: <'.$k.'>'."\r\n\r\n";
                    $alternative .= chunk_split(base64_encode($content))."\r\n\r\n";
                }
                $alternative .= '--'.$related_bnd.'--'."\r\n\r\n";
            } else {
                $alternative .= 'Content-Type: text/html; charset="utf-8"'."\r\n";
                $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
                $alternative .= $result."\r\n\r\n";
            }
            $alternative .= '--'.$result_bnd.'--';
            $result = $alternative;
        }

        if ($this->hasAttachments()) {
            $bnd = '==Multipart_Boundary_x'.md5(microtime()).'x';
            $this->setHeader('MIME-Version', '1.0;');
            $this->setHeader('Content-Type', 'multipart/mixed; '."\r\n\t".'boundary="'.$bnd.'"');

            $message = '';
            $message .= '--'.$bnd."\r\n";
            if ($this->html) {
                $message .= 'Content-Type: multipart/alternative; '."\r\n\t".'boundary="'.$result_bnd.'"'."\r\n";
            } else {
                $message .= 'Content-Type: text/plain; charset="utf-8"'."\r\n";
            }
            $message .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
            $message .= $result."\r\n\r\n";

            foreach ($this->attached as $file) {
                $content = &$file[0]->content();
                if (!$content) {
                    continue;
                }
                $size = strlen($content);
                $content = chunk_split(base64_encode($content));
                $message .= '--'.$bnd."\r\n";
                $message .= 'Content-Type: application/octet-stream; name="'.'=?utf-8?B?'.base64_encode($file[1] ? $file[1] : $file[0]->name).'?='.'"'."\r\n";
                $message .= 'Content-Disposition: attachment; size='.$size."\r\n";
                $message .= 'Content-Transfer-Encoding: base64'."\r\n\r\n";
                $message .= $content."\r\n\r\n";
            }
            $message .= '--'.$bnd.'--';
        } else {
            $this->setHeader('MIME-Version', '1.0;');
            if ($this->html) {
                $this->setHeader('Content-Type', 'multipart/alternative; '."\r\n\t".'boundary="'.$result_bnd.'"');
            } else {
                $this->setHeader('Content-Type', 'text/plain; charset="utf-8"');
            }
            $message = $result;
        }

        $headers = [];
        $content = null;
        foreach ($this->headers as $k => $v) {
            if ($this->crt && $k === 'Content-Type') {
                $content = $k.': '.$v;
                continue;
            }
            $headers[] = $k.': '.$v;
        }
        $headers[] = 'Date: '.date('r');
        if (count($to)) {
            $headers[] = 'To: '.implode(', ', $to);
        }
        if (count($cc)) {
            $headers[] = 'CC: '.implode(', ', $cc);
        }
        if (count($bcc)) {
            $headers[] = 'BCC: '.implode(', ', $bcc);
        }
        $headers[] = 'Subject: =?utf-8?B?'.base64_encode((string) $this->subject).'?=';
        $headers[] = 'Message-ID: <'.md5(implode('', $headers).$message.microtime()).($this->from ? '.'.$this->from : '@local.dev').'>';
        $headers = implode("\r\n", $headers);

        if ($this->crt) {
            $file = tempnam(sys_get_temp_dir(), 'mail');
            $sign = tempnam(sys_get_temp_dir(), 'sign');
            file_put_contents($file, ($content ? $content."\r\n\r\n" : '').$message);
            $rslt = false;
            if (!$this->ca) {
                $rslt = openssl_pkcs7_sign($file, $sign, 'file://'.$this->crt, $this->pass ? array('file://'.$this->key, $this->pass) : 'file://'.$this->key, null);
            } else {
                $rslt = openssl_pkcs7_sign($file, $sign, 'file://'.$this->crt, $this->pass ? array('file://'.$this->key, $this->pass) : 'file://'.$this->key, null, PKCS7_DETACHED, 'file://'.$this->ca);
            }
            if (!$rslt) {
                throw new \vakata\mail\MailException('Could not sign');
            }
            $rslt = file_get_contents($sign);
            $rslt = explode("\n\n", str_replace(array("\r\n", "\r"), "\n", $rslt), 2);
            $headers .= "\r\n".str_replace("\n", "\r\n", $rslt[0]);
            $message = $rslt[1];
            @unlink($file);
            @unlink($signed);
        }

        if ($sender === null) {
            $sender = new \vakata\mail\send\MailSender();
        }

        return $sender->send($t, $c, $b, $this->from, $this->subject, $headers, $message);
    }
}
