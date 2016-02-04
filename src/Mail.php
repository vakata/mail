<?php

namespace vakata\mail;

/**
 * A class representing an e-mail message (headers, body, etc).
 */
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

    /**
     * Create an instance. Optionally supply initial values for from / subject and the email body.
     * @method __construct
     * @param  string      $from    the from field, can be either an email or First Last <email@addesss.com>
     * @param  string      $subject the email subject
     * @param  string      $message the message body
     */
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

    protected function parseParts($body)
    {
        $body = str_replace(["\r\n", "\n"], ["\n", "\r\n"], $body);
        list($headers, $body) = explode("\r\n\r\n", $body, 2);
        $headers = array_filter(explode("\r\n", preg_replace("(\r\n\s+)", " ", $headers)));
        foreach ($headers as $k => $v) {
            $v = explode(':', $v, 2);
            $headers[$this->cleanHeaderName($v[0])] = trim($v[1]);
            unset($headers[$k]);
        }
        if (!isset($headers['Content-Type']) || strpos($headers['Content-Type'], 'multipart') === false) {
            if (isset($headers['Content-Transfer-Encoding'])) {
                switch (strtolower($headers['Content-Transfer-Encoding'])) {
                    case 'base64':
                        $body = base64_decode($body);
                        break;
                    case 'quoted-printable':
                        $body = quoted_printable_decode($body);
                        break;
                    default:
                        break;
                }
            }
            $type = 'text/plain';
            if (isset($headers['Content-Type'])) {
                $type = explode(';', $headers['Content-Type'], 2)[0];
            }
            return [
                'head' => $headers,
                'type' => $type,
                'body' => $body
            ];
        }

        // multipart
        $type = explode(';', explode('multipart/', $headers['Content-Type'], 2)[1], 2)[0];
        $bndr = trim(explode(' boundary=', $headers['Content-Type'])[1], '"');
        $parts = explode("\r\n" . '--' . $bndr, "\r\n" . $body);
        array_pop($parts);
        array_shift($parts);
        $rslt = [
            'head' => $headers,
            'type' => $type,
            'body' => []
        ];
        foreach ($parts as $part) {
            $rslt['body'][] = $this->parseParts($part);
        }
        return $rslt;
    }
    protected function processPart(&$part, $mode = 'main')
    {
        switch ($part['type']) {
            case 'text/plain':
                if (!$this->message) {
                    $this->setMessage($part['body'], false);
                }
                break;
            case 'text/html':
                $this->setMessage($part['body'], true);
                break;
            default:
                if (is_array($part['body'])) {
                    foreach ($part['body'] as $item) {
                        $this->processPart($item, $part['type']);
                    }
                } else {
                    if ($mode === 'mixed') {
                        $name = 'attachment';
                        if (isset($part['head']['Content-Type']) && strpos($part['head']['Content-Type'], 'name=')) {
                            $name = trim(explode('name=', $part['head']['Content-Type'], 2)[1], '"');
                        }
                        $this->addAttachment($part['body'], $name);
                    }
                    // depends that the root object is the first one: https://tools.ietf.org/html/rfc2387
                    if ($mode === 'related' && $this->isHTML() && isset($part['head']['Content-Id'])) {
                        $body = $this->getMessage();
                        $relid = trim($part['head']['Content-Id'], '<>');
                        $related = 'data:' . $part['type'] . ';base64,' . base64_encode($part['body']);
                        $body = str_replace('cid:' . $relid, $related, $body);
                        $this->setMessage($body, true);
                    }
                }
                break;
        }
    }
    /**
     * Create an instance from a stringified mail.
     * @method fromString
     * @param  string     $str the mail string
     * @return \vakata\mail\Mail          the mail instance
     */
    public static function fromString($mail)
    {
        $rtrn = new self();
        $mail = $rtrn->parseParts($mail);
        foreach ($mail['head'] as $k => $v) {
            switch (strtolower($k)) {
                case 'to':
                    $rtrn->setTo($v);
                    break;
                case 'cc':
                    $rtrn->setCc($v);
                    break;
                case 'bcc':
                    $rtrn->setBcc($v);
                    break;
                case 'from':
                    $rtrn->setFrom($v);
                    break;
                case 'subject':
                    $rtrn->setSubject($v);
                    break;
                default:
                    $rtrn->setHeader($k, $v);
                    break;
            }
        }
        $rtrn->processPart($mail);
        return $rtrn;
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

    /**
     * Retrieve the recipients.
     * @method getTo
     * @param  boolean $mailOnly should only email addresses be included (instead of Name <address>), defaults to false
     * @return array             array of to addresses
     */
    public function getTo($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) {
                return $v['mail'];
            }, $this->to) :
            $this->to;
    }
    /**
     * Set the recipients.
     * @method setTo
     * @param  string|array $mail the new recipients
     * @return self
     */
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
            $this->setHeader(
                'To',
                implode(',', array_map(function ($v) {
                    return $v['string'];
                }, $this->to))
            );
        }

        return $this;
    }
    /**
     * Retrieve the carbon copy recipients.
     * @method getCc
     * @param  boolean $mailOnly should only email addresses be included (instead of Name <address>), defaults to false
     * @return array             array of to addresses
     */
    public function getCc($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) {
                return $v['mail'];
            }, $this->cc) :
            $this->cc;
    }
    /**
     * Set the carbon copy recipients.
     * @method setCc
     * @param  string|array $mail the new recipients
     * @return self
     */
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
            $this->setHeader(
                'CC',
                implode(',', array_map(function ($v) {
                    return $v['string'];
                }, $this->cc))
            );
        }

        return $this;
    }
    /**
     * Retrieve the blind carbon copy recipients.
     * @method getBcc
     * @param  boolean $mailOnly should only email addresses be included (instead of Name <address>), defaults to false
     * @return array             array of to addresses
     */
    public function getBcc($mailOnly = false)
    {
        return $mailOnly ?
            array_map(function ($v) {
                return $v['mail'];
            }, $this->bcc) :
            $this->bcc;
    }
    /**
     * Set the blind carbon copy recipients.
     * @method setBcc
     * @param  string|array $mail the new recipients
     * @return self
     */
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
            $this->setHeader(
                'BCC',
                implode(',', array_map(function ($v) {
                    return $v['string'];
                }, $this->bcc))
            );
        }

        return $this;
    }
    /**
     * Get the sender.
     * @method getFrom
     * @param  boolean $mailOnly should only an email address be included (instead of Name <address>), defaults to false
     * @return string            the sender data
     */
    public function getFrom($mailOnly = false)
    {
        return $mailOnly ? $this->getAddress($this->from) : $this->from;
    }
    /**
     * Set the sender.
     * @method setFrom
     * @param  string  $mail the new sender
     * @return self
     */
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
    /**
     * Get the message subject.
     * @method getSubject
     * @return string     the message subject
     */
    public function getSubject()
    {
        return $this->subject;
    }
    /**
     * Set the message subject (and also set the appropriate headers).
     * @method setSubject
     * @param  self
     */
    public function setSubject($subject)
    {
        if (strpos($subject, '=?') === 0) {
            $subject = explode('?', substr($subject, 0, -2), 4);
            $subject = base64_decode(array_pop($subject));
        }
        $this->subject = $subject;
        $this->setHeader('Subject', '=?utf-8?B?'.base64_encode((string) $this->subject).'?=');
        return $this;
    }
    /**
     * Get the message body.
     * @method getMessage
     * @return string     the message body
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * Set the message body.
     * @method setMessage
     * @param  string     $message the new message body
     * @param  boolean    $isHTML  is the body HTML formatted (or plain text), defaults to true.
     */
    public function setMessage($message, $isHTML = true)
    {
        $this->message = $message;
        $this->html = $isHTML;

        return $this;
    }
    /**
     * Is the message HTML formatted.
     * @method isHTML
     * @return boolean
     */
    public function isHTML()
    {
        return $this->html;
    }
    /**
     * Retrieve all set headers.
     * @method getHeaders
     * @return array     all headers of the message
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    /**
     * Add a header to the message.
     * @method setHeader
     * @param  string    $header the header name
     * @param  string    $value  the header value
     * @return  self
     */
    public function setHeader($header, $value)
    {
        $this->headers[$this->cleanHeaderName($header)] = $value;

        return $this;
    }
    /**
     * Is a specific header set on the message.
     * @method hasHeader
     * @param  string    $header the header name
     * @return boolean
     */
    public function hasHeader($header)
    {
        return isset($this->headers[$this->cleanHeaderName($header)]);
    }
    /**
     * Retieve a header value by name.
     * @method getHeader
     * @param  string    $header the header name
     * @return string            the header value
     */
    public function getHeader($header)
    {
        return isset($this->headers[$this->cleanHeaderName($header)]) ?
            $this->headers[$this->cleanHeaderName($header)] :
            null;
    }
    /**
     * Remove a header from the message by name.
     * @method removeHeader
     * @param  string       $header the header name
     * @return self
     */
    public function removeHeader($header)
    {
        unset($this->headers[$this->cleanHeaderName($header)]);

        return $this;
    }
    /**
     * Remove all headers from the message.
     * @method removeHeaders
     * @return self
     */
    public function removeHeaders()
    {
        $this->headers = [];

        return $this;
    }
    /**
     * Does the message have attachments.
     * @method hasAttachments
     * @return int the attachments count
     */
    public function hasAttachments()
    {
        return count($this->attached);
    }
    /**
     * Add an attachment to the message.
     * @method addAttachment
     * @param  string        $content the contents of the attachment
     * @param  string        $name    the file name for the attachment
     * @return  self
     */
    public function addAttachment($content, $name)
    {
        if (!is_string($content) || !strlen($content)) {
            throw new MailException('Invalid content');
        }
        $this->attached[] = [ &$content, $name ];

        return $this;
    }
    /**
     * Retieve a list of all attachments.
     * @method getAttachments
     * @return array         all attached documents
     */
    public function getAttachments()
    {
        return $this->attached;
    }
    /**
     * Remove all attachments.
     * @method removeAttachments
     * @return self
     */
    public function removeAttachments()
    {
        $this->attached = [];

        return $this;
    }
    /**
     * Prepare the message for signing.
     * @method sign
     * @param  string $crt  path to the public key
     * @param  string $key  path to the private key
     * @param  string $pass the private key password (if necessary)
     * @param  string $ca   the CA chain file
     * @return self
     */
    public function sign($crt, $key, $pass = null, $ca = null)
    {
        $this->crt = $crt ? realpath($crt) : null;
        $this->key = $key ? realpath($key) : null;
        $this->pass = $pass;
        $this->ca = $ca ? realpath($ca) : null;

        return $this;
    }
    /**
     * Get the ready message as a string (headers and body)
     * @method __toString
     * @return string     the whole message
     */
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
                $images = [];
                $relatedBnd = '==Related_Boundary_x'.md5(microtime()).'x';
                $alternative .= 'Content-Type: multipart/related; '."\r\n\t".'boundary="'.$relatedBnd.'"'."\r\n\r\n";
                $alternative .= '--'.$relatedBnd."\r\n";
                $alternative .= 'Content-Type: text/html; charset="utf-8"'."\r\n";
                $alternative .= 'Content-Transfer-Encoding: 8bit'."\r\n\r\n";
                $alternative .= preg_replace_callback(
                    [
                        '(\<img(.*?)src\s*=\s*"([^"]+)")is',
                        '(\<img(.*?)src\s*=\s*\'([^\']+)\')is',
                        '(\<img(.*?)src=([^\'" ]+))is'
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
                    if (substr($image, 0, 5) === 'data:') {
                        list($mime, $content) = explode(';', substr($image, 5), 2);
                        $mime = explode('/', $mime);
                        if ($mime[0] !== 'image' || substr($content, 0, 6) !== 'base64') {
                            continue;
                        }
                        $content = substr($content, 6);
                        $extn = $mime[1];
                        $mime = implode('/', $mime);
                    } else {
                        $content = file_get_contents($image);
                        $fnfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = @finfo_buffer($fnfo, $content);
                        finfo_close($fnfo);
                        $extn = basename($image);
                        $extn = substr($extn, strrpos($extn, '.') + 1);
                        $content = base64_encode($content);
                    }
                    if (!$mime) {
                        continue;
                    }
                    $alternative .= '--'.$relatedBnd."\r\n";
                    $alternative .= 'Content-Type: '.$mime.'; name="'.md5($k).'.'.$extn.'"'."\r\n";
                    $alternative .= 'Content-Transfer-Encoding: base64'."\r\n";
                    $alternative .= 'Content-ID: <'.$k.'>'."\r\n\r\n";
                    $alternative .= chunk_split($content)."\r\n\r\n";
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
