# vakata\mail\Mail
A class representing an e-mail message (headers, body, etc).

## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\mail\mail__construct)|Create an instance. Optionally supply initial values for from / subject and the email body.|
|[getTo](#vakata\mail\mailgetto)|Retrieve the recipients.|
|[setTo](#vakata\mail\mailsetto)|Set the recipients.|
|[getCc](#vakata\mail\mailgetcc)|Retrieve the carbon copy recipients.|
|[setCc](#vakata\mail\mailsetcc)|Set the carbon copy recipients.|
|[getBcc](#vakata\mail\mailgetbcc)|Retrieve the blind carbon copy recipients.|
|[setBcc](#vakata\mail\mailsetbcc)|Set the blind carbon copy recipients.|
|[getFrom](#vakata\mail\mailgetfrom)|Get the sender.|
|[setFrom](#vakata\mail\mailsetfrom)|Set the sender.|
|[getSubject](#vakata\mail\mailgetsubject)|Get the message subject.|
|[setSubject](#vakata\mail\mailsetsubject)|Set the message subject (and also set the appropriate headers).|
|[getMessage](#vakata\mail\mailgetmessage)|Get the message body.|
|[setMessage](#vakata\mail\mailsetmessage)|Set the message body.|
|[isHTML](#vakata\mail\mailishtml)|Is the message HTML formatted.|
|[getHeaders](#vakata\mail\mailgetheaders)|Retrieve all set headers.|
|[setHeader](#vakata\mail\mailsetheader)|Add a header to the message.|
|[hasHeader](#vakata\mail\mailhasheader)|Is a specific header set on the message.|
|[getHeader](#vakata\mail\mailgetheader)|Retieve a header value by name.|
|[removeHeader](#vakata\mail\mailremoveheader)|Remove a header from the message by name.|
|[removeHeaders](#vakata\mail\mailremoveheaders)|Remove all headers from the message.|
|[hasAttachments](#vakata\mail\mailhasattachments)|Does the message have attachments.|
|[addAttachment](#vakata\mail\mailaddattachment)|Add an attachment to the message.|
|[getAttachments](#vakata\mail\mailgetattachments)|Retieve a list of all attachments.|
|[removeAttachments](#vakata\mail\mailremoveattachments)|Remove all attachments.|
|[sign](#vakata\mail\mailsign)|Prepare the message for signing.|
|[__toString](#vakata\mail\mail__tostring)|Get the ready message as a string (headers and body)|

---



### vakata\mail\Mail::__construct
Create an instance. Optionally supply initial values for from / subject and the email body.  


```php
public function __construct (  
    string $from,  
    string $subject,  
    string $message  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$from` | `string` | the from field, can be either an email or First Last <email@addesss.com> |
| `$subject` | `string` | the email subject |
| `$message` | `string` | the message body |

---


### vakata\mail\Mail::getTo
Retrieve the recipients.  


```php
public function getTo (  
    boolean $mailOnly  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$mailOnly` | `boolean` | should only email addresses be included (instead of Name <address>), defaults to false |
|  |  |  |
| `return` | `array` | array of to addresses |

---


### vakata\mail\Mail::setTo
Set the recipients.  


```php
public function setTo (  
    string|array $mail  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$mail` | `string`, `array` | the new recipients |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::getCc
Retrieve the carbon copy recipients.  


```php
public function getCc (  
    boolean $mailOnly  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$mailOnly` | `boolean` | should only email addresses be included (instead of Name <address>), defaults to false |
|  |  |  |
| `return` | `array` | array of to addresses |

---


### vakata\mail\Mail::setCc
Set the carbon copy recipients.  


```php
public function setCc (  
    string|array $mail  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$mail` | `string`, `array` | the new recipients |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::getBcc
Retrieve the blind carbon copy recipients.  


```php
public function getBcc (  
    boolean $mailOnly  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$mailOnly` | `boolean` | should only email addresses be included (instead of Name <address>), defaults to false |
|  |  |  |
| `return` | `array` | array of to addresses |

---


### vakata\mail\Mail::setBcc
Set the blind carbon copy recipients.  


```php
public function setBcc (  
    string|array $mail  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$mail` | `string`, `array` | the new recipients |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::getFrom
Get the sender.  


```php
public function getFrom (  
    boolean $mailOnly  
) : string    
```

|  | Type | Description |
|-----|-----|-----|
| `$mailOnly` | `boolean` | should only an email address be included (instead of Name <address>), defaults to false |
|  |  |  |
| `return` | `string` | the sender data |

---


### vakata\mail\Mail::setFrom
Set the sender.  


```php
public function setFrom (  
    string $mail  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$mail` | `string` | the new sender |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::getSubject
Get the message subject.  


```php
public function getSubject () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the message subject |

---


### vakata\mail\Mail::setSubject
Set the message subject (and also set the appropriate headers).  


```php
public function setSubject (  
    self   
)   
```

|  | Type | Description |
|-----|-----|-----|
| `` | `self` |  |

---


### vakata\mail\Mail::getMessage
Get the message body.  


```php
public function getMessage () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the message body |

---


### vakata\mail\Mail::setMessage
Set the message body.  


```php
public function setMessage (  
    string $message,  
    boolean $isHTML  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$message` | `string` | the new message body |
| `$isHTML` | `boolean` | is the body HTML formatted (or plain text), defaults to true. |

---


### vakata\mail\Mail::isHTML
Is the message HTML formatted.  


```php
public function isHTML () : boolean    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `boolean` |  |

---


### vakata\mail\Mail::getHeaders
Retrieve all set headers.  


```php
public function getHeaders () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | all headers of the message |

---


### vakata\mail\Mail::setHeader
Add a header to the message.  


```php
public function setHeader (  
    string $header,  
    string $value  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$header` | `string` | the header name |
| `$value` | `string` | the header value |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::hasHeader
Is a specific header set on the message.  


```php
public function hasHeader (  
    string $header  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$header` | `string` | the header name |
|  |  |  |
| `return` | `boolean` |  |

---


### vakata\mail\Mail::getHeader
Retieve a header value by name.  


```php
public function getHeader (  
    string $header  
) : string    
```

|  | Type | Description |
|-----|-----|-----|
| `$header` | `string` | the header name |
|  |  |  |
| `return` | `string` | the header value |

---


### vakata\mail\Mail::removeHeader
Remove a header from the message by name.  


```php
public function removeHeader (  
    string $header  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$header` | `string` | the header name |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::removeHeaders
Remove all headers from the message.  


```php
public function removeHeaders () : self    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::hasAttachments
Does the message have attachments.  


```php
public function hasAttachments () : int    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `int` | the attachments count |

---


### vakata\mail\Mail::addAttachment
Add an attachment to the message.  


```php
public function addAttachment (  
    string $content,  
    string $name  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$content` | `string` | the contents of the attachment |
| `$name` | `string` | the file name for the attachment |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::getAttachments
Retieve a list of all attachments.  


```php
public function getAttachments () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | all attached documents |

---


### vakata\mail\Mail::removeAttachments
Remove all attachments.  


```php
public function removeAttachments () : self    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::sign
Prepare the message for signing.  


```php
public function sign (  
    string $crt,  
    string $key,  
    string $pass,  
    string $ca  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$crt` | `string` | path to the public key |
| `$key` | `string` | path to the private key |
| `$pass` | `string` | the private key password (if necessary) |
| `$ca` | `string` | the CA chain file |
|  |  |  |
| `return` | `self` |  |

---


### vakata\mail\Mail::__toString
Get the ready message as a string (headers and body)  


```php
public function __toString ()   
```

|  | Type | Description |
|-----|-----|-----|

---

