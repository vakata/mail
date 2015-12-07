<?php
namespace vakata\kvstore\test;

class StorageTest extends \PHPUnit_Framework_TestCase
{
	public static function setUpBeforeClass() {
	}
	public static function tearDownAfterClass() {
	}
	protected function setUp() {
	}
	protected function tearDown() {
	}

	public function testCreate() {
		$mail = new \vakata\mail\Mail('test@asdf.com', 'Test subject', 'Test message');

		$this->assertEquals($mail->getFrom(), 'test@asdf.com');
		$this->assertEquals($mail->getSubject(), 'Test subject');
		$this->assertEquals($mail->getMessage(), 'Test message');
		$this->assertEquals($mail->hasHeader('Date'), true);
		$this->assertEquals($mail->hasHeader('Message-ID'), true);
	}
	public function testFrom() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setFrom('test@asdf.com')->getFrom(), 'test@asdf.com');
		$this->assertEquals($mail->setFrom('invalid')->getFrom(), null);
		$this->assertEquals($mail->setFrom('Name Family <test@asdf.com>')->getFrom(true), 'test@asdf.com');
		$this->assertEquals($mail->setFrom('Name Family <test@asdf.com>')->getFrom(false), "=?utf-8?B?TmFtZSBGYW1pbHk=?= <test@asdf.com>");
		$this->assertEquals($mail->setFrom('Name Family <test@asdf.com>')->getHeader('From'), "=?utf-8?B?TmFtZSBGYW1pbHk=?= <test@asdf.com>");
	}
	public function testSubject() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setSubject('asdf')->getSubject(), 'asdf');
	}
	public function testMessage() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setMessage('asdf')->getMessage(), 'asdf');
		$this->assertEquals($mail->setMessage('asdf')->isHTML(), true);
		$this->assertEquals($mail->setMessage('asdf', false)->isHTML(), false);
	}
	public function testTo() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setTo('invalid')->getTo(), []);
		$this->assertEquals($mail->setTo('Name Family <test@asdf.com>')->getTo(), [['mail' => 'test@asdf.com', 'string' => "=?utf-8?B?TmFtZSBGYW1pbHk=?= <test@asdf.com>"]]);
		$this->assertEquals($mail->setTo('Name Family <test@asdf.com>')->getTo(true), ["test@asdf.com"]);
		$this->assertEquals($mail->setTo('Name Family <test@asdf.com>, Name 2 Family <test2@asdf.com>, ')->getTo(true), ["test@asdf.com", "test2@asdf.com"]);
		$this->assertEquals($mail->setTo(['Name Family <test@asdf.com>', 'test3@asdf.com'])->getTo(true), ["test@asdf.com", "test3@asdf.com"]);
		$this->assertEquals($mail->setTo(['Name Family <test@asdf.com>', 'invalid'])->getTo(true), ["test@asdf.com"]);
	}
	public function testCC() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setCc('invalid')->getCc(), []);
		$this->assertEquals($mail->setCc('Name Family <test@asdf.com>')->getCc(), [['mail' => 'test@asdf.com', 'string' => "=?utf-8?B?TmFtZSBGYW1pbHk=?= <test@asdf.com>"]]);
		$this->assertEquals($mail->setCc('Name Family <test@asdf.com>')->getCc(true), ["test@asdf.com"]);
		$this->assertEquals($mail->setCc('Name Family <test@asdf.com>, Name 2 Family <test2@asdf.com>, ')->getCc(true), ["test@asdf.com", "test2@asdf.com"]);
		$this->assertEquals($mail->setCc(['Name Family <test@asdf.com>', 'test3@asdf.com'])->getCc(true), ["test@asdf.com", "test3@asdf.com"]);
		$this->assertEquals($mail->setCc(['Name Family <test@asdf.com>', 'invalid'])->getCc(true), ["test@asdf.com"]);
	}
	public function testBCC() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setBcc('invalid')->getBcc(), []);
		$this->assertEquals($mail->setBcc('Name Family <test@asdf.com>')->getBcc(), [['mail' => 'test@asdf.com', 'string' => "=?utf-8?B?TmFtZSBGYW1pbHk=?= <test@asdf.com>"]]);
		$this->assertEquals($mail->setBcc('Name Family <test@asdf.com>')->getBcc(true), ["test@asdf.com"]);
		$this->assertEquals($mail->setBcc('Name Family <test@asdf.com>, Name 2 Family <test2@asdf.com>, ')->getBcc(true), ["test@asdf.com", "test2@asdf.com"]);
		$this->assertEquals($mail->setBcc(['Name Family <test@asdf.com>', 'test3@asdf.com'])->getBcc(true), ["test@asdf.com", "test3@asdf.com"]);
		$this->assertEquals($mail->setBcc(['Name Family <test@asdf.com>', 'invalid'])->getBcc(true), ["test@asdf.com"]);
	}
	public function testHeaders() {
		$mail = new \vakata\mail\Mail();

		$this->assertEquals($mail->setHeader('Test', 'asdf')->hasHeader('Test'), true);
		$this->assertEquals($mail->setHeader('test_name', 'asdf')->hasHeader('Test-Name'), true);
		$this->assertEquals($mail->setHeader('test_name', 'asdf')->getHeader('Test-Name'), 'asdf');
		$this->assertEquals($mail->removeHeader('test_name', 'asdf')->getHeader('Test-Name'), null);
		$this->assertEquals(count($mail->getheaders()) > 0, true);
		$this->assertEquals($mail->removeHeaders()->getheaders(), []);
	}
	public function testAttachments() {
		$mail = new \vakata\mail\Mail();
		$data1 = 'asdf';
		$data2 = 'zxcv';

		$this->assertEquals($mail->getAttachments(), []);
		$this->assertEquals($mail->addAttachment($data1, 'file1.txt')->getAttachments(), [['asdf','file1.txt']]);
		$this->assertEquals($mail->addAttachment($data2, 'file2.txt')->getAttachments(), [['asdf','file1.txt'], ['zxcv','file2.txt']]);
		$this->assertEquals($mail->removeAttachments()->getAttachments(), []);
	}
}