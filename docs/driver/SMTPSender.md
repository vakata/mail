# vakata\mail\driver\SMTPSender  

A mail sender class that sends message using an SMTP server.

## Implements:
vakata\mail\driver\SenderInterface



## Methods

| Name | Description |
|------|-------------|
|[__construct](#smtpsender__construct)|Create an instance.|
|[__destruct](#smtpsender__destruct)||
|[connect](#smtpsenderconnect)||
|[connected](#smtpsenderconnected)||
|[disconnect](#smtpsenderdisconnect)||
|[imap](#smtpsenderimap)|A static method used to authenticate against an IMAP server (some SMTP servers require this)|
|[pop](#smtpsenderpop)|A static method used to authenticate against a POP server (some SMTP servers require this)|
|[send](#smtpsendersend)|Send a message.|




### SMTPSender::__construct  

**Description**

```php
public __construct (string $connection, string $user, string $pass)
```

Create an instance. 

 

**Parameters**

* `(string) $connection`
: the server connection string (for example `smtp://user:pass@server:port/`)  
* `(string) $user`
: optional way to provide the username (if not included in the connection string)  
* `(string) $pass`
: optional way to provide the password (if not included in the connection string)  

**Return Values**




### SMTPSender::__destruct  

**Description**

```php
public __destruct (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### SMTPSender::connect  

**Description**

```php
public connect (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### SMTPSender::connected  

**Description**

```php
public connected (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### SMTPSender::disconnect  

**Description**

```php
public disconnect (void)
```

 

 

**Parameters**

`This function has no parameters.`

**Return Values**




### SMTPSender::imap  

**Description**

```php
public static imap (string $connection, string $user, string $pass)
```

A static method used to authenticate against an IMAP server (some SMTP servers require this) 

 

**Parameters**

* `(string) $connection`
: the server connection string (for example `imap://user:pass@server:port/`)  
* `(string) $user`
: optional way to provide the username (if not included in the connection string)  
* `(string) $pass`
: optional way to provide the password (if not included in the connection string)  

**Return Values**




### SMTPSender::pop  

**Description**

```php
public static pop (string $connection, string $user, string $pass)
```

A static method used to authenticate against a POP server (some SMTP servers require this) 

 

**Parameters**

* `(string) $connection`
: the server connection string (for example `pop://user:pass@server:port/`)  
* `(string) $user`
: optional way to provide the username (if not included in the connection string)  
* `(string) $pass`
: optional way to provide the password (if not included in the connection string)  

**Return Values**




### SMTPSender::send  

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



