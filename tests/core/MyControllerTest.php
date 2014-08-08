<?php

class MyControllerTest extends Todo_TestCase {

	public function setUp()
	{
		parent::setUp();
	}

	public function testValidate()
	{
		$actual = $this->CI->validate('1990-03-09','due_date');
		$this->assertTrue($actual);
	}

	public function testValidateInvalidMethod()
	{
		$this->setExpectedException(
			'InvalidArgumentException',
			"Validation callback 'bar' does not exist"
		);
		$this->CI->validate('foo', 'bar');
	}

}
// End of MyControllerTest.php