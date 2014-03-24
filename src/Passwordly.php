<?php

namespace Innoscience\Passwordly;

/**
 * Class Passwordly
 * @package Innoscience\Passwordly
 */
class Passwordly {

	/**
	 * @var string
	 */
	protected static $lowerPool = 'abcdefghijklmnopqrstuvwxyz';
	/**
	 * @var string
	 */
	protected static $upperPool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	/**
	 * @var string
	 */
	protected static $numPool = '0123456789';
	/**
	 * @var string
	 */
	protected static $symbolPool = '~`!@#$%^&*()_-+={[}]|\:;"\',<.>/?';
	/**
	 * @var null
	 */
	protected static $disableOpenssl = null;

	/**
	 * @var null
	 */
	protected $strict = null;
	/**
	 * @var array
	 */
	protected $parameters = array();
	/**
	 * @var array
	 */
	protected $errors = array();

	/**
	 * @return static
	 */
	public static function can() {
		return new static;
	}

	/**
	 * @param $characterPool
	 */
	public static function setLowerPool($characterPool) {
		static::$lowerPool = $characterPool;
	}

	/**
	 * @param $characterPool
	 */
	public static function setUpperPool($characterPool) {
		static::$upperPool = $characterPool;
	}

	/**
	 * @param $characterPool
	 */
	public static function setNumberPool($characterPool) {
		static::$numPool = $characterPool;
	}

	/**
	 * @param $characterPool
	 */
	public static function setSymbolPool($characterPool) {
		static::$symbolPool = $characterPool;
	}

	/**
	 * @return string
	 */
	public static function getLowerPool() {
		return static::$lowerPool;
	}

	/**
	 * @return string
	 */
	public static function getUpperPool() {
		return static::$upperPool;
	}

	/**
	 * @return string
	 */
	public static function getNumberPool() {
		return static::$numPool;
	}

	/**
	 * @return string
	 */
	public static function getSymbolPool() {
		return static::$symbolPool;
	}

	/**
	 * @param $bool
	 */
	public static function setDisableOpenssl($bool) {
		static::$disableOpenssl = $bool;
	}

	/**
	 * @param int $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasLower($min = 1, $max = null) {
		$this->addQualifier('lower', $min, $max);
		return $this;
	}

	/**
	 * @param int $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasUpper($min = 1, $max = null) {
		$this->addQualifier('upper', $min, $max);
		return $this;
	}

	/**
	 * @param int $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasNumbers($min = 1, $max = null) {
		$this->addQualifier('numbers', $min, $max);
		return $this;
	}

	/**
	 * @param int $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasSymbols($min = 1, $max = null) {
		$this->addQualifier('symbols', $min, $max);
		return $this;
	}

	/**
	 * @param int $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasSpaces($min = 1, $max = null) {
		$this->addQualifier('spaces', $min, $max);
		return $this;
	}

	/**
	 * @param $min
	 * @param null $max
	 *
	 * @return $this
	 */
	public function hasLength($min, $max = null) {
		$this->addQualifier('length', $min, $max);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function strict() {
		$this->strict = true;
		return $this;
	}

	/**
	 * @return array
	 */
	public function errors() {
		return $this->errors;
	}

	/**
	 * @param $name
	 * @param int $min
	 * @param null $max
	 */
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

	/**
	 * @param $name
	 *
	 * @return null
	 */
	protected function getLimit($name) {
		if (!isset($this->parameters[$name]['limit'])) {
			return null;
		}

		return $this->parameters[$name]['limit'];
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	protected function getMin($name) {
		if (!isset($this->parameters[$name]['min'])) {
			return null;
		}
		return $this->parameters[$name]['min'];
	}

	/**
	 * @param $name
	 *
	 * @return null
	 */
	protected function getMax($name) {
		if (!isset($this->parameters[$name]['max'])) {
			return null;
		}
		return $this->parameters[$name]['max'];
	}

	/**
	 * @param $name
	 * @param $num
	 *
	 * @return mixed
	 */
	protected function setLimit($name, $num) {
		return $this->parameters[$name]['limit'] = $num;
	}

	/**
	 * @param $name
	 * @param $num
	 *
	 * @return mixed
	 */
	protected function setMin($name, $num) {
		return $this->parameters[$name]['min'] = $num;
	}

	/**
	 * @param $name
	 * @param $num
	 *
	 * @return mixed
	 */
	protected function setMax($name, $num) {
		return $this->parameters[$name]['max'] = $num;
	}

	/**
	 * @param $password
	 *
	 * @return bool
	 */
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

		if ($this->strict && $this->getMax('lower') && $this->checkPool(static::getLowerPool(), $password) > $this->getMax('lower')) {
			$this->errors['lower'] = "Password must contain at least ".$this->getMax('lower')." lower-case characters.";
		}

		if ($this->strict && $this->getMax('upper') && $this->checkPool(static::getUpperPool(), $password) > $this->getMax('upper')) {
			$this->errors['upper'] = "Password must contain at least ".$this->getMin('upper')." upper-case characters.";
		}

		if ($this->strict && $this->getMax('numbers') && $this->checkPool(static::getNumberPool(), $password) > $this->getMax('numbers')) {
			$this->errors['numbers'] = "Password must contain at least ".$this->getMin('numbers')." numbers.";
		}

		if ($this->strict && $this->getMax('symbols') && $this->checkPool(static::getSymbolPool(), $password) > $this->getMax('symbols')) {
			$this->errors['symbols'] = "Password must contain at least ".$this->getMax('symbols')." non-alphanumeric characters.";
		}

		if ($this->strict && $this->getMax('spaces') && $this->checkPool(' ', $password) > $this->getMax('spaces')) {
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

	/**
	 * @param $characterPool
	 * @param $password
	 *
	 * @return int
	 */
	protected function checkPool($characterPool, $password) {
		$count = 0;
		for ($curChar = 0; $curChar < strlen($password); $curChar++) {
			$count += substr_count($characterPool, $password[$curChar]);
		}
		return $count;
	}

	/**
	 * @param $characterPool
	 *
	 * @return string
	 */
	protected function getRandomChar($characterPool) {
		return substr($characterPool, rand(0, strlen($characterPool) - 1), 1);
	}

	/**
	 * @param $length
	 *
	 * @return string
	 */
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

	/**
	 * @param null $length
	 * @param null $maxLength
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function generate($length = null, $maxLength = null) {
		if ($length) {
			$this->addQualifier('length', $length, $maxLength);
		}

		if (!$this->getLimit('length')) {
			throw new \Exception("Password length is required.");
		}

		$minValidLength = $this->getLimit('lower') + $this->getLimit('upper') +$this->getLimit('numbers') + $this->getLimit('symbols') + $this->getLimit('pools');

		if ($this->getMin('length') < $minValidLength) {
			throw new \Exception("Password length [".$this->getMin('length')."] cannot be less than number of qualifiers [{$minValidLength}].");
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