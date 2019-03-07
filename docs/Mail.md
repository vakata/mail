# vakata\mail\Mail  

A class representing an e-mail message (headers, body, etc).

## Implements:
vakata\mail\MailInterface



## Methods

| Name | Description |
|------|-------------|
|[__construct](#mail__construct)|Create an instance. Optionally supply initial values for from / subject and the email body.|
|[__toString](#mail__tostring)|Get the ready message as a string (headers and body)|
|[addAttachment](#mailaddattachment)|Add an attachment to the message.|
|[fromString](#mailfromstring)|Create an instance from a stringified mail.|
|[getAttachments](#mailgetattachments)|Retieve a list of all attachments.|
|[getBcc](#mailgetbcc)|Retrieve the blind carbon copy recipients.|
|[getCc](#mailgetcc)|Retrieve the carbon copy recipients.|
|[getFrom](#mailgetfrom)|Get the sender.|
|[getHeader](#mailgetheader)|Retieve a header value by name.|
|[getHeaders](#mailgetheaders)|Retrieve all set headers.|
|[getMessage](#mailgetmessage)|Get the message body.|
|[getRelated](#mailgetrelated)|Retieve a list of all related.|
|[getSubject](#mailgetsubject)|Get the message subject.|
|[getTo](#mailgetto)|Retrieve the recipients.|
|[hasAttachments](#mailhasattachments)|Does the message have attachments.|
|[hasHeader](#mailhasheader)|Is a specific header set on the message.|
|[htmlToText](#mailhtmltotext)|A static helper function to convert HTML to text.|
|[isHTML](#mailishtml)|Is the message HTML formatted.|
|[removeAttachments](#mailremoveattachments)|Remove all attachments.|
|[removeHeader](#mailremoveheader)|Remove a header from the message by name.|
|[removeHeaders](#mailremoveheaders)|Remove all headers from the message.|
|[rfc1342decode](#mailrfc1342decode)||
|[rfc1342encode](#mailrfc1342encode)||
|[setBcc](#mailsetbcc)|Set the blind carbon copy recipients.|
|[setCc](#mailsetcc)|Set the carbon copy recipients.|
|[setFrom](#mailsetfrom)|Set the sender.|
|[setHeader](#mailsetheader)|Add a header to the message.|
|[setMessage](#mailsetmessage)|Set the message body.|
|[setSubject](#mailsetsubject)|Set the message subject (and also set the appropriate headers).|
|[setTo](#mailsetto)|Set the recipients.|
|[sign](#mailsign)|Prepare the message for signing.|




### Mail::__construct  

**Description**

```php
public __construct (string $from, string $subject, string $message)
```

Create an instance. Optionally supply initial values for from / subject and the email body. 

 

**Parameters**

* `(string) $from`
: the from field, can be either an email or First Last <email@addesss.com>  
* `(string) $subject`
: the email subject  
* `(string) $message`
: the message body  

**Return Values**




### Mail::__toString  

**Description**

```php
public __toString (void)
```

Get the ready message as a string (headers and body) 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> the whole message  




### Mail::addAttachment  

**Description**

```php
public addAttachment (string $content, string $name)
```

Add an attachment to the message. 

 

**Parameters**

* `(string) $content`
: the contents of the attachment  
* `(string) $name`
: the file name for the attachment  

**Return Values**

`self`





### Mail::fromString  

**Description**

```php
public static fromString (string $str)
```

Create an instance from a stringified mail. 

 

**Parameters**

* `(string) $str`
: the mail string  

**Return Values**

`\vakata\mail\Mail`

> the mail instance  




### Mail::getAttachments  

**Description**

```php
public getAttachments (void)
```

Retieve a list of all attachments. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`

> all attached documents  




### Mail::getBcc  

**Description**

```php
public getBcc (boolean $mailOnly)
```

Retrieve the blind carbon copy recipients. 

 

**Parameters**

* `(boolean) $mailOnly`
: should only email addresses be included (instead of Name <address>), defaults to false  

**Return Values**

`array`

> array of to addresses  




### Mail::getCc  

**Description**

```php
public getCc (boolean $mailOnly)
```

Retrieve the carbon copy recipients. 

 

**Parameters**

* `(boolean) $mailOnly`
: should only email addresses be included (instead of Name <address>), defaults to false  

**Return Values**

`array`

> array of to addresses  




### Mail::getFrom  

**Description**

```php
public getFrom (boolean $mailOnly)
```

Get the sender. 

 

**Parameters**

* `(boolean) $mailOnly`
: should only an email address be included (instead of Name <address>), defaults to false  

**Return Values**

`string`

> the sender data  




### Mail::getHeader  

**Description**

```php
public getHeader (string $header)
```

Retieve a header value by name. 

 

**Parameters**

* `(string) $header`
: the header name  

**Return Values**

`string`

> the header value  




### Mail::getHeaders  

**Description**

```php
public getHeaders (void)
```

Retrieve all set headers. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`

> all headers of the message  




### Mail::getMessage  

**Description**

```php
public getMessage (void)
```

Get the message body. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> the message body  




### Mail::getRelated  

**Description**

```php
public getRelated (void)
```

Retieve a list of all related. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`

> all attached documents  




### Mail::getSubject  

**Description**

```php
public getSubject (void)
```

Get the message subject. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`

> the message subject  




### Mail::getTo  

**Description**

```php
public getTo (boolean $mailOnly)
```

Retrieve the recipients. 

 

**Parameters**

* `(boolean) $mailOnly`
: should only email addresses be included (instead of Name <address>), defaults to false  

**Return Values**

`array`

> array of to addresses  




### Mail::hasAttachments  

**Description**

```php
public hasAttachments (void)
```

Does the message have attachments. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`int`

> the attachments count  




### Mail::hasHeader  

**Description**

```php
public hasHeader (string $header)
```

Is a specific header set on the message. 

 

**Parameters**

* `(string) $header`
: the header name  

**Return Values**

`boolean`





### Mail::htmlToText  

**Description**

```php
public static htmlToText (string $html)
```

A static helper function to convert HTML to text. 

 

**Parameters**

* `(string) $html`
: the HTML to convert  

**Return Values**

`string`

> the plain text data from the HTML string  




### Mail::isHTML  

**Description**

```php
public isHTML (void)
```

Is the message HTML formatted. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`boolean`





### Mail::removeAttachments  

**Description**

```php
public removeAttachments (void)
```

Remove all attachments. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`self`





### Mail::removeHeader  

**Description**

```php
public removeHeader (string $header)
```

Remove a header from the message by name. 

 

**Parameters**

* `(string) $header`
: the header name  

**Return Values**

`self`





### Mail::removeHeaders  

**Description**

```php
public removeHeaders (void)
```

Remove all headers from the message. 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`self`





### Mail::rfc1342decode  

**Description**

```php
public static rfc1342decode (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### Mail::rfc1342encode  

**Description**

```php
public static rfc1342encode (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### Mail::setBcc  

**Description**

```php
public setBcc (string|array $mail)
```

Set the blind carbon copy recipients. 

 

**Parameters**

* `(string|array) $mail`
: the new recipients  

**Return Values**

`self`





### Mail::setCc  

**Description**

```php
public setCc (string|array $mail)
```

Set the carbon copy recipients. 

 

**Parameters**

* `(string|array) $mail`
: the new recipients  

**Return Values**

`self`





### Mail::setFrom  

**Description**

```php
public setFrom (string $mail)
```

Set the sender. 

 

**Parameters**

* `(string) $mail`
: the new sender  

**Return Values**

`self`





### Mail::setHeader  

**Description**

```php
public setHeader (string $header, string $value)
```

Add a header to the message. 

 

**Parameters**

* `(string) $header`
: the header name  
* `(string) $value`
: the header value  

**Return Values**

`self`





### Mail::setMessage  

**Description**

```php
public setMessage (string $message, boolean $isHTML, boolean $related)
```

Set the message body. 

 

**Parameters**

* `(string) $message`
: the new message body  
* `(boolean) $isHTML`
: is the body HTML formatted (or plain text), defaults to true.  
* `(boolean) $related`
: should related items be extracted  

**Return Values**




### Mail::setSubject  

**Description**

```php
public setSubject (self )
```

Set the message subject (and also set the appropriate headers). 

 

**Parameters**

* `(self) `

**Return Values**




### Mail::setTo  

**Description**

```php
public setTo (string|array $mail)
```

Set the recipients. 

 

**Parameters**

* `(string|array) $mail`
: the new recipients  

**Return Values**

`self`





### Mail::sign  

**Description**

```php
public sign (string $crt, string $key, string $pass, string $ca)
```

Prepare the message for signing. 

 

**Parameters**

* `(string) $crt`
: path to the public key  
* `(string) $key`
: path to the private key  
* `(string) $pass`
: the private key password (if necessary)  
* `(string) $ca`
: the CA chain file  

**Return Values**

`self`




