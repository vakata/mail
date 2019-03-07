# vakata\mail\driver\MailSender  

A mail sender class that sends message using the built-in PHP mail() function.

## Implements:
vakata\mail\driver\SenderInterface



## Methods

| Name | Description |
|------|-------------|
|[send](#mailsendersend)|Send a message.|




### MailSender::send  

**Description**

```php
public send (\vakata\mail\MailInterface $mail)
```

Send a message. 

 

**Parameters**

* `(\vakata\mail\MailInterface) $mail`
: the message to be sent  

**Return Values**

`array`

> array with two keys - 'good' and 'fail' - indicating successfull and failed addresses  



