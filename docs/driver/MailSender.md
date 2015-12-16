# vakata\mail\driver\MailSender
A mail sender class that sends message using the built-in PHP mail() function.

## Methods

| Name | Description |
|------|-------------|
|[send](#vakata\mail\driver\mailsendersend)|Send a message.|

---



### vakata\mail\driver\MailSender::send
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

