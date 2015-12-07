<?php

namespace vakata\mail;

class Mail implements MailInterface
{
    protected $to = [];
    protected $cc = [];
    protected $bcc = [];
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
        if ($from) {
            $this->setFrom($from);
        }
        if ($subject) {
            $this->setSubject($subject);
        }
        if ($message) {
            $this->setMessage($message);
        }
        $this->setHeader('Date', date('r'));
        $this->setHeader(
            'Message-ID',
            '<' . microtime(true) . '.' . ($this->from ? $this->from : '@local.dev') . '>'
        );
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

    public function getTo($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) { return $v['mail']; }, $this->to) :
            $this->to;
    }
    public function setTo($mail)
    {
        if (!is_array($mail)) {
            $mail = explode(',', $mail);
        }
        $this->to = [];
        foreach ($mail as $m) {
            $temp = $this->getAddress($m);
            if ($temp) {
                $this->to[] = [ 'mail' => $temp, 'string' => $this->getAddressString($m) ];
            }
        }
        $this->removeHeader('To');
        if (count($this->to)) {
            $this->setHeader('To', implode(',', array_map(function ($v) { return $v['string']; }, $this->to)));
        }

        return $this;
    }
    public function getCc($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) { return $v['mail']; }, $this->cc) :
            $this->cc;
    }
    public function setCc($mail)
    {
        if (!is_array($mail)) {
            $mail = explode(',', $mail);
        }
        $this->cc = [];
        foreach ($mail as $m) {
            $temp = $this->getAddress($m);
            if ($temp) {
                $this->cc[] = [ 'mail' => $temp, 'string' => $this->getAddressString($m) ];
            }
        }
        $this->removeHeader('CC');
        if (count($this->cc)) {
            $this->setHeader('CC', implode(',', array_map(function ($v) { return $v['string']; }, $this->cc)));
        }

        return $this;
    }
    public function getBcc($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) { return $v['mail']; }, $this->bcc) :
            $this->bcc;
    }
    public function setBcc($mail)
    {
        if (!is_array($mail)) {
            $mail = explode(',', $mail);
        }
        $this->bcc = [];
        foreach ($mail as $m) {
            $temp = $this->getAddress($m);
            if ($temp) {
                $this->bcc[] = [ 'mail' => $temp, 'string' => $this->getAddressString($m) ];
            }
        }
        $this->removeHeader('BCC');
        if (count($this->bcc)) {
            $this->setHeader('BCC', implode(',', array_map(function ($v) { return $v['string']; }, $this->bcc)));
        }

        return $this;
    }
    public function getFrom($mailOnly = false)
    {
        return $mailOnly ? $this->getAddress($this->from) : $this->from;
    }
    public function setFrom($mail)
    {
        $this->from = null;
        $temp = $this->getAddress($mail);
        if ($temp) {
            $this->from = $this->getAddressString($mail);
            $this->setHeader('From', $this->from);
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
        $this->setHeader('Subject', '=?utf-8?B?'.base64_encode((string) $this->subject).'?=');
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
        return isset($this->headers[$this->cleanHeaderName($header)]) ?
            $this->headers[$this->cleanHeaderName($header)] :
            null;
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
    public function addAttachment(&$content, $name)
    {
        if (!is_string($content) || !strlen($content)) {
            throw new MailException('Invalid content');
        }
        $this->attached[] = [ $content, $name ];

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

    public function __toString()
    {
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

        $resultBnd = '==Alternative_Boundary_x'.md5(microtime()).'x';
        if ($this->html) {
            $alternative = '';
            $alternative .= '--'.$resultBnd."\r\n";
            $alternative .= 'Content-Type: text/plain; charset="utf-8"'."\r\n";
            $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
            $alternative .= strip_tags($result)."\r\n\r\n";
            $alternative .= '--'.$resultBnd."\r\n";
            if (strpos($result, '<img ') !== false) {
                $relatedBnd = '==Related_Boundary_x'.md5(microtime()).'x';
                $alternative .= 'Content-Type: multipart/related; '."\r\n\t".'boundary="'.$relatedBnd.'"'."\r\n\r\n";
                $alternative .= '--'.$relatedBnd."\r\n";
                $alternative .= 'Content-Type: text/html; charset="utf-8"'."\r\n";
                $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
                $alternative .= preg_replace_callback(
                    [
                        '(\<img(.*?)src\s*=\s*"([^"]+)")i',
                        '(\<img(.*?)src\s*=\s*\'([^\']+)\')i',
                        '(\<img(.*?)src=([^\'" ]+))i'
                    ],
                    function ($matches) use (&$images) {
                        $k = md5($matches[2]).'@local.dev';
                        $images[$k] = $matches[2];
                        return '<img '.$matches[1].' src="cid:'.$k.'" ';
                    },
                    $result
                );
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
                    $alternative .= '--'.$relatedBnd."\r\n";
                    $alternative .= 'Content-Type: '.$mime.'; name="'.md5($k).'.'.$extn.'"'."\r\n";
                    $alternative .= 'Content-Transfer-Encoding: base64'."\r\n";
                    $alternative .= 'Content-ID: <'.$k.'>'."\r\n\r\n";
                    $alternative .= chunk_split(base64_encode($content))."\r\n\r\n";
                }
                $alternative .= '--'.$relatedBnd.'--'."\r\n\r\n";
            } else {
                $alternative .= 'Content-Type: text/html; charset="utf-8"'."\r\n";
                $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
                $alternative .= $result."\r\n\r\n";
            }
            $alternative .= '--'.$resultBnd.'--';
            $result = $alternative;
        }

        if ($this->hasAttachments()) {
            $bnd = '==Multipart_Boundary_x'.md5(microtime()).'x';
            $this->setHeader('MIME-Version', '1.0;');
            $this->setHeader('Content-Type', 'multipart/mixed; '."\r\n\t".'boundary="'.$bnd.'"');

            $message = '';
            $message .= '--'.$bnd."\r\n";
            if ($this->html) {
                $message .= 'Content-Type: multipart/alternative; '."\r\n\t".'boundary="'.$resultBnd.'"'."\r\n";
            } else {
                $message .= 'Content-Type: text/plain; charset="utf-8"'."\r\n";
            }
            $message .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
            $message .= $result."\r\n\r\n";

            foreach ($this->attached as &$file) {
                $content = $file[0];
                $size = strlen($content);
                $content = chunk_split(base64_encode($content));
                $message .= '--'.$bnd."\r\n";
                $message .= 'Content-Type: application/octet-stream; name="';
                $message .= '=?utf-8?B?'.base64_encode($file[1]).'?=';
                $message .= '"'."\r\n";
                $message .= 'Content-Disposition: attachment; size='.$size."\r\n";
                $message .= 'Content-Transfer-Encoding: base64'."\r\n\r\n";
                $message .= $content."\r\n\r\n";
            }
            $message .= '--'.$bnd.'--';
        } else {
            $this->setHeader('MIME-Version', '1.0;');
            if ($this->html) {
                $this->setHeader('Content-Type', 'multipart/alternative; '."\r\n\t".'boundary="'.$resultBnd.'"');
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
        $headers = implode("\r\n", $headers);

        if ($this->crt) {
            $file = tempnam(sys_get_temp_dir(), 'mail');
            $sign = tempnam(sys_get_temp_dir(), 'sign');
            file_put_contents($file, ($content ? $content."\r\n\r\n" : '').$message);
            $rslt = false;
            if (!$this->ca) {
                $rslt = openssl_pkcs7_sign(
                    $file,
                    $sign,
                    'file://'.$this->crt,
                    $this->pass ? array('file://'.$this->key, $this->pass) : 'file://'.$this->key,
                    null
                );
            } else {
                $rslt = openssl_pkcs7_sign(
                    $file,
                    $sign,
                    'file://'.$this->crt,
                    $this->pass ? array('file://'.$this->key, $this->pass) : 'file://'.$this->key,
                    null,
                    PKCS7_DETACHED,
                    'file://'.$this->ca
                );
            }
            if (!$rslt) {
                throw new \vakata\mail\MailException('Could not sign');
            }
            $rslt = file_get_contents($sign);
            $rslt = explode("\n\n", str_replace(array("\r\n", "\r"), "\n", $rslt), 2);
            $headers .= "\r\n".str_replace("\n", "\r\n", $rslt[0]);
            $message = $rslt[1];
            @unlink($file);
            @unlink($sign);
        }
        return $headers . "\r\n\r\n" . $message;
    }
}
