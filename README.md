# mail

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Code Climate][ico-cc]][link-cc]
[![Tests Coverage][ico-cc-coverage]][link-cc]

A few simple mail sender class.

## Install

Via Composer

``` bash
$ composer require vakata/mail
```

## Usage

``` php
// build the message
$mail = new \vakata\mail\Mail();
$mail
    ->setFrom('Name Family <mail@domain.tld>') // or simply an email
    ->setSubject('Testmail') // unicode is fine too
    ->setMessage('Check this pic out <img src="http://url.to/pic" />')
    ->setTo(['first@recipient.tld', 'Second Person <second@recipient.tld>'])
    ->setCc('mail@domain.tld')
    ->setBcc('bcc@domain.tld');

// send the message
$sender = new \vakata\mail\driver\SMTPSender('ssl://user:pass@host:port');
// PHP mail() is also supported:
// $sender = new \vakata\mail\driver\MailSender();

$sender->send($mail); // return an array of good and bad emails
```

Read more in the [API docs](docs/README.md)

## Testing

``` bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email github@vakata.com instead of using the issue tracker.

## Credits

- [vakata][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/vakata/mail.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vakata/mail/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/vakata/mail.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/vakata/mail.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/vakata/mail.svg?style=flat-square
[ico-cc]: https://img.shields.io/codeclimate/github/vakata/mail.svg?style=flat-square
[ico-cc-coverage]: https://img.shields.io/codeclimate/coverage/github/vakata/mail.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vakata/mail
[link-travis]: https://travis-ci.org/vakata/mail
[link-scrutinizer]: https://scrutinizer-ci.com/g/vakata/mail/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/vakata/mail
[link-downloads]: https://packagist.org/packages/vakata/mail
[link-author]: https://github.com/vakata
[link-contributors]: ../../contributors
[link-cc]: https://codeclimate.com/github/vakata/mail

