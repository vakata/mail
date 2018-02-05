<?php

namespace vakata\mail\driver;

use vakata\mail\MailException;
use \vakata\mail\MailInterface;

/**
 * A mail sender class that sends message using an SMTP server.
 */
class SMTPSender implements SenderInterface
{
    protected $connection = null;

    protected function host()
    {
        if (isset($_SERVER) && isset($_SERVER['SERVER_NAME']) && !empty($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }
        if (function_exists('gethostname')) {
            $temp = gethostname();
            if ($temp !== false) {
                return $temp;
            }
        }
        $temp = php_uname('n');
        if ($temp !== false) {
            return $temp;
        }

        return 'local.dev';
    }

    protected function read()
    {
        stream_set_timeout($this->connection, 300);
        $str = '';
        while (is_resource($this->connection) && !feof($this->connection)) {
            $tmp = @fgets($this->connection, 515);
            $str .= $tmp;
            if ((isset($tmp[3]) && $tmp[3] == ' ')) {
                break;
            }
        }

        return $str;
    }

    protected function data($data)
    {
        fwrite($this->connection, $data);
    }

    protected function comm($data, array $expect = [])
    {
        $this->data($data."\r\n");
        $data = $this->read();
        $code = substr($data, 0, 3);
        $data = substr($data, 4);
        if (count($expect) && !in_array($code, $expect)) {
            throw new MailException('SMTP Error : '.$code . ' ' . $data);
        }

        return $data;
    }
    protected function helo()
    {
        $host = $this->host();
        try {
            $data = $this->comm('EHLO '.$host, [250]);
        } catch (MailException $e) {
            $data = $this->comm('HELO '.$host, [250]);
        }
        // parse hello fields
        $smtp = array();
        $data = explode("\n", $data);
        foreach ($data as $n => $s) {
            $s = trim(substr($s, 4));
            if (!$s) {
                continue;
            }
            $s = explode(' ', $s);
            if (!empty($s)) {
                if (!$n) {
                    $n = 'HELO';
                    $s = $s[0];
                } else {
                    $n = array_shift($s);
                    if ($n == 'SIZE') {
                        $s = ($s) ? $s[0] : 0;
                    }
                }
                $smtp[$n] = ($s ? $s : true);
            }
        }
        return $smtp;
    }

    /**
     * Create an instance.
     * @param  string      $connection the server connection string (for example `smtp://user:pass@server:port/`)
     * @param  string $user optional way to provide the username (if not included in the connection string)
     * @param  string $pass optional way to provide the password (if not included in the connection string)
     */
    public function __construct($connection, $user = null, $pass = null)
    {
        $connection = parse_url($connection); // host, port, user, pass
        if ($connection === false) {
            throw new MailException('Could not parse SMTP config');
        }
        if (!isset($connection['user'])) {
            $connection['user'] = $user;
        }
        if (!isset($connection['pass'])) {
            $connection['pass'] = $pass;
        }

        $errn = 0;
        $errs = '';
        set_time_limit(300); // default is 5 minutes
        $this->connection = stream_socket_client(
            (isset($connection['scheme']) && $connection['scheme'] === 'ssl' ? 'ssl://' : '').$connection['host'].':'.(isset($connection['port']) ? $connection['port'] : 25),
            $errn,
            $errs,
            300 // default is 5 minutes
        );

        if (!is_resource($this->connection)) {
            throw new MailException('Could not connect to SMTP server');
        }

        $this->read(); // get announcement if any
        $smtp = $this->helo();
        if (isset($connection['scheme']) && $connection['scheme'] === 'tls') {
            $this->comm('STARTTLS', [220]);
            if (!stream_socket_enable_crypto($this->connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new MailException('Could not secure connection');
            }
            $smtp = $this->helo();
        }
        if (isset($connection['user'])) {
            $username = $connection['user'];
            $password = isset($connection['pass']) ? $connection['pass'] : '';
            $auth = 'LOGIN';
            if (isset($smtp['AUTH']) && is_array($smtp['AUTH'])) {
                foreach (['LOGIN', 'CRAM-MD5', 'PLAIN'] as $a) {
                    if (in_array($a, $smtp['AUTH'])) {
                        $auth = $a;
                        break;
                    }
                }
            }
            switch ($auth) {
                case 'PLAIN':
                    $this->comm('AUTH PLAIN', [334]);
                    $this->comm(base64_encode("\0".$username."\0".$password), [235]);
                    break;
                case 'LOGIN':
                    $this->comm('AUTH LOGIN', [334]);
                    $this->comm(base64_encode($username), [334]);
                    $this->comm(base64_encode($password), [235]);
                    break;
                case 'CRAM-MD5':
                    $challenge = $this->comm('AUTH CRAM-MD5', [334]);
                    $challenge = base64_decode($challenge);
                    $this->comm(base64_encode($username.' '.hash_hmac('md5', $challenge, $password)), [235]);
            }
        }
    }

    public function __destruct()
    {
        try {
            if (is_resource($this->connection)) {
                $this->comm('QUIT', [221]);
                @fclose($this->connection);
            }
            $this->connection = null;
        } catch (\Exception $ignore) {
        }
    }
    /**
     * A static method used to authenticate against a POP server (some SMTP servers require this)
     * @param  string $connection the server connection string (for example `pop://user:pass@server:port/`)
     * @param  string $user optional way to provide the username (if not included in the connection string)
     * @param  string $pass optional way to provide the password (if not included in the connection string)
     */
    public static function pop($connection, $user = null, $pass = null)
    {
        $connection = parse_url($connection); // host, port, user, pass
        if ($connection === false) {
            throw new MailException('Could not parse POP config');
        }
        if (!isset($connection['user'])) {
            $connection['user'] = $user;
        }
        if (!isset($connection['pass'])) {
            $connection['pass'] = $pass;
        }

        $errn = 0;
        $errs = '';
        set_time_limit(30);
        $pop = @stream_socket_client(
            $connection['host'].':'.(isset($connection['port']) ? $connection['port'] : 110),
            $errn,
            $errs,
            30
        );

        if (!is_resource($pop)) {
            throw new MailException('Could not connect to POP server');
        }

        try {
            stream_set_timeout($pop, 30);
            if (substr(fgets($pop, 512), 0, 3) !== '+OK') {
                throw new MailException('Error reading POP server');
            }
            if (isset($connection['user'])) {
                fwrite($pop, 'USER '.$connection['user']."\r\n");
                if (substr(fgets($pop, 512), 0, 3) !== '+OK') {
                    throw new MailException('Error reading POP server');
                }
                fwrite($pop, 'PASS '.(isset($connection['pass']) ? $connection['pass'] : '')."\r\n");
                if (substr(fgets($pop, 512), 0, 3) !== '+OK') {
                    throw new MailException('Error reading POP server');
                }
                try {
                    if (is_resource($pop)) {
                        fwrite($pop, 'QUIT');
                        @fclose($pop);
                    }
                    $pop = null;
                } catch (\Exception $ignore) {
                }
            }
        } catch (\Exception $e) {
            try {
                if (is_resource($pop)) {
                    fwrite($pop, 'QUIT');
                    @fclose($pop);
                }
                $pop = null;
            } catch (\Exception $ignore) {
            }
            throw $e;
        }
    }
    /**
     * A static method used to authenticate against an IMAP server (some SMTP servers require this)
     * @param  string $connection the server connection string (for example `imap://user:pass@server:port/`)
     * @param  string $user optional way to provide the username (if not included in the connection string)
     * @param  string $pass optional way to provide the password (if not included in the connection string)
     */
    public static function imap($connection, $user = null, $pass = null)
    {
        $connection = parse_url($connection); // host, port, user, pass
        if ($connection === false) {
            throw new MailException('Could not parse IMAP config');
        }
        if (!isset($connection['user'])) {
            $connection['user'] = $user;
        }
        if (!isset($connection['pass'])) {
            $connection['pass'] = $pass;
        }

        $errn = 0;
        $errs = '';
        set_time_limit(30);
        $imap = @stream_socket_client(
            $connection['host'].':'.(isset($connection['port']) ? $connection['port'] : 143),
            $errn,
            $errs,
            30
        );

        if (!is_resource($imap)) {
            throw new MailException('Could not connect to IMAP server');
        }

        try {
            stream_set_timeout($imap, 30);
            if (substr(fgets($imap, 512), 0, 4) !== '* OK') {
                throw new MailException('Error reading IMAP server');
            }
            if (!isset($connection['user']) || !isset($connection['pass'])) {
                throw new MailException('No credentials supplied for IMAP server');
            }
            fwrite($imap, 'a1 LOGIN '.$connection['user'].' '.$connection['pass']."\r\n");
            if (substr(fgets($imap, 512), 0, 11) !== 'a1 OK LOGIN') {
                throw new MailException('Invalid credentials for IMAP server');
            }
            try {
                if (is_resource($imap)) {
                    fwrite($imap, 'a2 LOGOUT');
                    @fclose($imap);
                }
                $imap = null;
            } catch (\Exception $ignore) {
            }
        } catch (\Exception $e) {
            try {
                if (is_resource($imap)) {
                    fwrite($imap, 'a2 LOGOUT');
                    @fclose($imap);
                }
                $imap = null;
            } catch (\Exception $ignore) {
            }
            throw $e;
        }
    }
    /**
     * Send a message.
     * @param  \vakata\mail\MailInterface $mail the message to be sent
     * @return array              array with two keys - 'good' and 'fail' - indicating successfull and failed addresses
     */
    public function send(MailInterface $mail)
    {
        $this->comm('MAIL FROM:<'.$mail->getFrom(true).'>', [250]);
        $recp = array_merge(
            $mail->getTo(true),
            $mail->getCc(true),
            $mail->getBcc(true)
        );
        $badr = [];
        $good = [];
        foreach ($recp as $v) {
            try {
                $this->comm('RCPT TO:<'.$v.'>', [250, 251]);
                $good[] = $v;
            } catch (MailException $e) {
                $badr[] = $v;
            }
        }
        if (count($good)) {
            $this->comm('DATA', [354]);
            $data = (string)$mail;

            $data = explode("\n", str_replace(array("\r\n", "\r"), "\n", $data));
            foreach ($data as $line) {
                if (isset($line[0]) && $line[0] === '.') {
                    $line = '.'.$line;
                }
                $this->data($line."\r\n");
            }
        }
        $this->comm('.', [250]);
        $this->comm('RSET', [250]);

        return [ 'good' => $good, 'fail' => $badr ];
    }
}
