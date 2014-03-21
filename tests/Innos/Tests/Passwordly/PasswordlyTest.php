<?php

use Innos\Passwordly\Passwordly;

class PasswordlyTest extends PHPUnit_Framework_TestCase {
	
	var $dummy = array();	

	function testGenerateLength() {
		$password = Passwordly::can()->hasLength(8)->generate();

		$this->assertEquals(8, strlen($password));
	}

	function testGenerateLower() {
		$password = Passwordly::can()->hasLower(8)->generate(8);

		$passwordCheck = preg_match_all('/[a-z]/', $password, $this->dummy);

		$this->assertGreaterThanOrEqual(8, $passwordCheck);
	}

	function testGenerateUpper() {
		$password = Passwordly::can()->hasUpper(8)->generate(8);

		$passwordCheck = preg_match_all('/[A-Z]/', $password, $this->dummy);

		$this->assertGreaterThanOrEqual(8, $passwordCheck);
	}

	function testGenerateNumbers() {
		$password = Passwordly::can()->hasNumbers(8)->generate(8);

		$passwordCheck = preg_match_all('/[0-9]/', $password, $this->dummy);

		$this->assertGreaterThanOrEqual(8, $passwordCheck);
	}

	function testGenerateSymbols() {
		$password = Passwordly::can()->hasSymbols(8)->generate(8);

		$passwordCheck = preg_match_all('/[~`!@#$%^&*()_={}+\[\]|\-\\\;:"\'<>,.\/?]/', $password, $this->dummy);

		$this->assertEquals(8, $passwordCheck);
	}

	function testGenerateSpaces() {
		$password = Passwordly::can()->hasSpaces(8)->generate(8);

		$passwordCheck = preg_match_all('/[ ]/', $password, $this->dummy);

		$this->assertEquals(8, $passwordCheck);
	}

	function testGenerateChained() {
		$password = Passwordly::can()->hasSymbols(1)->hasUpper(1)->hasNumbers(1)->generate(8);

		$passwordCheck = preg_match_all('/[\w~`!@#$%^&*()_={}+\[\]|\-\\\;:"\'<>,.\/?]/', $password, $this->dummy);

		$this->assertGreaterThanOrEqual(8, $passwordCheck);
	}

	function testGenerateRanged() {
		$password = Passwordly::can()->hasSymbols(1)->hasUpper(1)->hasNumbers(1)->generate(8, 16);

		$this->assertGreaterThanOrEqual(8, strlen($password));
	}

	function testGenerateRandomLimits() {
		$password = Passwordly::can()->hasSymbols(1,3)->hasUpper(1,3)->hasNumbers(1,2)->generate(8);

		$this->assertEquals(8, strlen($password));
	}

	function testCheckLength() {
		$password = 'abcabcab123';
		$check = Passwordly::can()->hasLength(8)->check($password);

		$this->assertTrue($check);
	}

	function testCheckLengthFails() {
		$password = 'abcabc';
		$check = Passwordly::can()->hasLength(8)->check($password);

		$this->assertFalse($check);
	}

	function testCheckLower() {
		$password = 'abcabcab';
		$check = Passwordly::can()->hasLower(8)->check($password);

		$this->assertTrue($check);
	}

	function testCheckLowerFails() {
		$password = 'ABCABCAB';
		$check = Passwordly::can()->hasLower(8)->check($password);

		$this->assertFalse($check);
	}

	function testCheckUpper() {
		$password = 'ABCABCAB';
		$check = Passwordly::can()->hasUpper(8)->check($password);

		$this->assertTrue($check);
	}

	function testCheckUpperFails() {
		$password = 'ABC12312';
		$check = Passwordly::can()->hasUpper(8)->check($password);

		$this->assertFalse($check);
	}

	function testCheckNumbers() {
		$password = '12345678';
		$check = Passwordly::can()->hasNumbers(8)->check($password);

		$this->assertTrue($check);
	}

	function testCheckNumbersFails() {
		$password = 'abc12345';
		$check = Passwordly::can()->hasNumbers(8)->check($password);

		$this->assertFalse($check);
	}

	function testCheckSymbols() {
		$password = '!@#$%^&*';
		$check = Passwordly::can()->hasSymbols(8)->check($password);

		$this->assertTrue($check);
	}

	function testCheckSymbolsFails() {
		$password = 'ab%!@c12345';
		$check = Passwordly::can()->hasSymbols(8)->check($password);

		$this->assertFalse($check);
	}

	function testCheckSpaces() {
		$password = 'a b c d e';
		$check = Passwordly::can()->hasSpaces(4)->check($password);

		$this->assertTrue($check);
	}

	function testCheckSpacesFails() {
		$password = 'abcea';
		$check = Passwordly::can()->hasSymbols(4)->check($password);

		$this->assertFalse($check);
	}

	function testCheckChained() {
		$password = 'abc123@#$ABC';
		$check = Passwordly::can()->hasUpper(1)->hasNumbers(1)->hasSymbols(1)->check($password);

		$this->assertTrue($check);
	}

	function testCheckErrors() {
		$password = 'abc';
		$passwordly = new Passwordly();
		$check = $passwordly->hasUpper(1)->hasNumbers(1)->hasSymbols(1)->hasSpaces(1)->hasLength(8)->check($password);

		$this->assertFalse($check);
		$this->assertEquals(count($passwordly->errors()), 5);
	}

	function testDualUsage() {
		//$password = 'abc123@#$ABC';
		$passwordly = new Passwordly;

		$password = $passwordly->hasUpper(1,3)->hasNumbers(1,3)->hasSymbols(1,3)->hasSpaces(1)->generate(16);
		$check = $passwordly->check($password);

		$this->assertEquals(strlen($password), 16);
		$this->assertTrue($check);
	}

	function testSimpleRandomStr() {
		Passwordly::setDisableOpenssl(TRUE);
		$passwordly = new Passwordly;
		$string = $passwordly->getRandomStr(16);

		$this->assertEquals(strlen($string), 16);
	}

	function testCheckStrictMode() {
		$password = 'abcabcab123';
		$check = Passwordly::can()->hasLength(8)->strict()->check($password);

		$this->assertTrue($check);
	}

	function testOverridePools() {
		Passwordly::setLowerPool('a');
		Passwordly::setUpperPool('b');
		Passwordly::setSymbolPool('c');
		Passwordly::setNumberPool('d');

		$this->assertEquals(1, strlen(Passwordly::getLowerPool()));
		$this->assertEquals(1, strlen(Passwordly::getUpperPool()));
		$this->assertEquals(1, strlen(Passwordly::getSymbolPool()));
		$this->assertEquals(1, strlen(Passwordly::getNumberPool()));
	}

	/**
	 * @expectedException Exception
	 */
	function testQualifiersException() {

		$password = Passwordly::can()->hasUpper(12)->generate(8);

	}

	/**
	 * @expectedException Exception
	 */
	function testLengthException() {

		$password = Passwordly::can()->hasUpper(12)->generate();

	}

}
 
