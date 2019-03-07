# vakata\mail\driver\FileSender  

A mail helper class that stores emails on the disk instead of sending them - useful for debugging.

## Implements:
vakata\mail\driver\SenderInterface



## Methods

| Name | Description |
|------|-------------|
|[__construct](#filesender__construct)|Create an instance.|
|[send](#filesendersend)|Send a message.|




### FileSender::__construct  

**Description**

```php
public __construct (string $dir)
```

Create an instance. 

 

**Parameters**

* `(string) $dir`
: the path to save all emails to  

**Return Values**




### FileSender::send  

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



