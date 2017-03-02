# vakata\mail\driver\SMTPSender
A mail sender class that sends message using an SMTP server.

## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\mail\driver\smtpsender__construct)|Create an instance.|
|[pop](#vakata\mail\driver\smtpsenderpop)|A static method used to authenticate against a POP server (some SMTP servers require this)|
|[imap](#vakata\mail\driver\smtpsenderimap)|A static method used to authenticate against an IMAP server (some SMTP servers require this)|
|[send](#vakata\mail\driver\smtpsendersend)|Send a message.|

---



### vakata\mail\driver\SMTPSender::__construct
Create an instance.  


```php
public function __construct (  
    string $connection,  
    string $user,  
    string $pass  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$connection` | `string` | the server connection string (for example `smtp://user:pass@server:port/`) |
| `$user` | `string` | optional way to provide the username (if not included in the connection string) |
| `$pass` | `string` | optional way to provide the password (if not included in the connection string) |

---


### vakata\mail\driver\SMTPSender::pop
A static method used to authenticate against a POP server (some SMTP servers require this)  


```php
public static function pop (  
    string $connection,  
    string $user,  
    string $pass  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$connection` | `string` | the server connection string (for example `pop://user:pass@server:port/`) |
| `$user` | `string` | optional way to provide the username (if not included in the connection string) |
| `$pass` | `string` | optional way to provide the password (if not included in the connection string) |

---


### vakata\mail\driver\SMTPSender::imap
A static method used to authenticate against an IMAP server (some SMTP servers require this)  


```php
public static function imap (  
    string $connection,  
    string $user,  
    string $pass  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$connection` | `string` | the server connection string (for example `imap://user:pass@server:port/`) |
| `$user` | `string` | optional way to provide the username (if not included in the connection string) |
| `$pass` | `string` | optional way to provide the password (if not included in the connection string) |

---


### vakata\mail\driver\SMTPSender::send
Send a message.  


```php
public function send (  
    \vakata\mail\MailInterface $mail  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$mail` | `\vakata\mail\MailInterface` | the message to be sent |
|  |  |  |
| `return` | `array` | array with two keys - 'good' and 'bad' - indicating successfull and failed addresses |

---

