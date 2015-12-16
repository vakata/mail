# vakata\mail\driver\FileSender
A mail helper class that stores emails on the disk instead of sending them - useful for debugging.

## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\mail\driver\filesender__construct)|Create an instance.|
|[send](#vakata\mail\driver\filesendersend)|Send a message.|

---



### vakata\mail\driver\FileSender::__construct
Create an instance.  


```php
public function __construct (  
    string $dir  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$dir` | `string` | the path to save all emails to |

---


### vakata\mail\driver\FileSender::send
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

