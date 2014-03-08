<?php

namespace Innos\Passwordly;
use \Exception;

class Passwordly {

	protected static $lowerPool = 'abcdefghijklmnopqrstuvwxyz';
	protected static $upperPool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	protected static $numPool = '0123456789';
	protected static $symbolPool = '~`!@#$%^&*()_-+={[}]|\:;"\',<.>/?';
	protected static $disableOpenssl = null;

	protected $strict = null;
	protected $parameters = array();
	protected $errors = array();

	public static function can() {
		return new static;
	}

	public static function setLowerPool($characterPool) {
		static::$lowerPool = $characterPool;
	}

	public static function setUpperPool($characterPool) {
		static::$upperPool = $characterPool;
	}

	public static function setNumberPool($characterPool) {
		static::$numPool = $characterPool;
	}

	public static function setSymbolPool($characterPool) {
		static::$symbolPool = $characterPool;
	}

	public static function getLowerPool() {
		return static::$lowerPool;
	}

	public static function getUpperPool() {
		return static::$upperPool;
	}

	public static function getNumberPool() {
		return static::$numPool;
	}

	public static function getSymbolPool() {
		return static::$symbolPool;
	}

	public static function setDisableOpenssl($bool) {
		static::$disableOpenssl = $bool;
	}

	public function hasLower($min = 1, $max = null) {
		$this->addQualifier('lower', $min, $max);
		return $this;
	}

	public function hasUpper($min = 1, $max = null) {
		$this->addQualifier('upper', $min, $max);
		return $this;
	}

	public function hasNumbers($min = 1, $max = null) {
		$this->addQualifier('numbers', $min, $max);
		return $this;
	}

	public function hasSymbols($min = 1, $max = null) {
		$this->addQualifier('symbols', $min, $max);
		return $this;
	}

	public function hasSpaces($min = 1, $max = null) {
		$this->addQualifier('spaces', $min, $max);
		return $this;
	}

	public function hasLength($min, $max = null) {
		$this->addQualifier('length', $min, $max);
		return $this;
	}

	public function strict() {
		$this->strict = true;
		return $this;
	}


	public function errors() {
		return $this->errors;
	}


	protected function addQualifier($name, $min = 1, $max = null) {
		$this->setMin($name, $min);
		$this->setMax($name, $max);
		
		if ($max) {
			$this->setLimit($name, rand($min, $max));
		}
		else {
			$this->setLimit($name, $min);
		}
	}

	protected function getLimit($name) {
		if (!isset($this->parameters[$name]['limit'])) {
			return null;
		}

		return $this->parameters[$name]['limit'];
	}

	protected function getMin($name) {
		if (!isset($this->parameters[$name]['min'])) {
			return null;
		}
		return $this->parameters[$name]['min'];
	}

	protected function getMax($name) {
		if (!isset($this->parameters[$name]['max'])) {
			return null;
		}
		return $this->parameters[$name]['max'];
	}

	protected function setLimit($name, $num) {
		return $this->parameters[$name]['limit'] = $num;
	}

	protected function setMin($name, $num) {
		return $this->parameters[$name]['min'] = $num;
	}

	protected function setMax($name, $num) {
		return $this->parameters[$name]['max'] = $num;
	}

	public function check($password) {

		$this->errors = array();

		if ($this->getLimit('lower') && $this->checkPool(static::getLowerPool(), $password) < $this->getMin('lower')) {
			$this->errors['lower'] = "Password must contain at least ".$this->getMin('lower')." lower-case characters.";
		}

		if ($this->getLimit('upper') && $this->checkPool(static::getUpperPool(), $password) < $this->getMin('upper')) {
			$this->errors['upper'] = "Password must contain at least ".$this->getMin('upper')." upper-case characters.";
		}

		if ($this->getLimit('numbers') && $this->checkPool(static::getNumberPool(), $password) < $this->getMin('numbers')) {
			$this->errors['numbers'] = "Password must contain at least ".$this->getMin('numbers')." numbers.";
		}

		if ($this->getLimit('symbols') && $this->checkPool(static::getSymbolPool(), $password) < $this->getMin('symbols')) {
			$this->errors['symbols'] = "Password must contain at least ".$this->getMin('symbols')." non-alphanumeric characters.";
		}

		if ($this->getLimit('spaces') && $this->checkPool(' ', $password) < $this->getMin('spaces')) {
			$this->errors['spaces'] = "Password must contain at least ".$this->getMin('spaces')." spaces.";
		}

		if ($this->getLimit('length') && strlen($password) < $this->getMin('length')) {
			$this->errors['length'] = "Password be at least ".$this->getMin('length')." characters in length.";
		}

		if ($this->strict && $this->getMax('lower') && $this->checkPool(static::getLowerPool(), $password) < $this->getMax('lower')) {
			$this->errors['lower'] = "Password must contain at least ".$this->getMax('lower')." lower-case characters.";
		}

		if ($this->strict && $this->getMax('upper') && $this->checkPool(static::getUpperPool(), $password) < $this->getMax('upper')) {
			$this->errors['upper'] = "Password must contain at least ".$this->getMin('upper')." upper-case characters.";
		}

		if ($this->strict && $this->getMax('numbers') && $this->checkPool(static::getNumberPool(), $password) < $this->getMax('numbers')) {
			$this->errors['numbers'] = "Password must contain at least ".$this->getMin('numbers')." numbers.";
		}

		if ($this->strict && $this->getMax('symbols') && $this->checkPool(static::getSymbolPool(), $password) < $this->getMax('symbols')) {
			$this->errors['symbols'] = "Password must contain at least ".$this->getMax('symbols')." non-alphanumeric characters.";
		}

		if ($this->strict && $this->getMax('spaces') && $this->checkPool(' ', $password) < $this->getMax('spaces')) {
			$this->errors['spaces'] = "Password must contain at least ".$this->getMax('spaces')." spaces.";
		}

		if ($this->strict && $this->getMax('length') && strlen($password) > $this->getMax('length')) {
			$this->errors['length'] = "Password less than ".$this->getMax('length')." characters in length.";
		}

		if ($this->errors) {
			return FALSE;
		}

		return TRUE;

	}

	protected function checkPool($characterPool, $password) {
		$count = 0;
		for ($curChar = 0; $curChar < strlen($password); $curChar++) {
			$count += substr_count($characterPool, $password[$curChar]);
		}
		return $count;
	}

	protected function getRandomChar($characterPool) {
		return substr($characterPool, rand(0, strlen($characterPool) - 1), 1);
	}

	public function getRandomStr($length) {
		$bytes = false;

		if (!static::$disableOpenssl) {
			$bytes = openssl_random_pseudo_bytes($length * 2);
		}

		if ($bytes === false || static::$disableOpenssl) {
			return substr(str_shuffle(str_repeat(static::getLowerPool().static::getNumberPool().static::getUpperPool(), 10)), 0, $length);
		}

		return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
	}

	public function generate($length = null, $maxLength = null) {
		if ($length) {
			$this->addQualifier('length', $length, $maxLength);
		}

		if (!$this->getLimit('length')) {
			throw new Exception("Password length is required.");
		}

		$minValidLength = $this->getLimit('lower') + $this->getLimit('upper') +$this->getLimit('numbers') + $this->getLimit('symbols') + $this->getLimit('pools');

		if ($this->getMin('length') < $minValidLength) {
			throw new Exception("Password length [".$this->getMin('length')."] cannot be less than number of qualifiers [{$minValidLength}].");
		}

		$passwordPool = '';

		if ($this->getLimit('lower')) {
			for ($i=1; $i <= $this->getLimit('lower'); $i++) {
				$passwordPool .= $this->getRandomChar(static::getLowerPool());
			}
		}

		if ($this->getLimit('upper')) {
			for ($i=1; $i <= $this->getLimit('upper'); $i++) {
				$passwordPool .= $this->getRandomChar(static::getUpperPool());
			}
		}

		if ($this->getLimit('numbers')) {
			for ($i=1; $i <= $this->getLimit('numbers'); $i++) {
				$passwordPool .= $this->getRandomChar(static::getNumberPool());
			}
		}

		if ($this->getLimit('symbols')) {
			for ($i=1; $i <= $this->getLimit('symbols'); $i++) {
				$passwordPool .= $this->getRandomChar(static::getSymbolPool());
			}
		}

		if ($this->getLimit('spaces')) {
			$passwordPool .= str_repeat(' ', $this->getLimit('spaces'));
		}

		if (strlen($passwordPool) < $this->getLimit('length')) {
			$passwordPool .= $this->getRandomStr($this->getLimit('length') - strlen($passwordPool));
		}

		return str_shuffle($passwordPool);

	}

}