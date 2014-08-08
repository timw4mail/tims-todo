<?php

class PageLibTest extends Todo_TestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testPageIsAutoloaded()
	{
		$this->assertInstanceOf('Page', $this->CI->page);
	}

	public function dataSetMessage()
	{
		return [
			'info' => [
				'args' => [
					'info',
					'Info test message'
				],
				'expected' => '<div class="message info">
	<span class="icon info"></span>
	Info test message<span class="icon close" onclick="this.parentElement.style.display=\'none\'"></span>
</div>'
			],
			'error' => [
				'args' => [
					'error',
					'Error test message'
				],
				'expected' => '<div class="message error">
	<span class="icon error"></span>
	Error test message<span class="icon close" onclick="this.parentElement.style.display=\'none\'"></span>
</div>'
			]
		];
	}

	/**
	 * @dataProvider dataSetMessage
	 */
	public function testSetMessage($args, $expected)
	{
		list($type, $message) = $args;

		$this->CI->page->set_message($type, $message);
		$actual = output();

		$this->assertEquals($expected, $actual);
	}

}