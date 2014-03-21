# Passwordly
A light-weight package for generating and checking passwords with requirements for PHP.

[![Build Status](https://travis-ci.org/innoscience/passwordly.png?branch=master)](https://travis-ci.org/innoscience/passwordly)

Copyright (C) 2014 Brandon Fenning

## Requirements

Compatible with PHP 5.3+

## Installation

Add `innoscience/passwordly` to the `composer.json` file:

	"require": {
        "innoscience/passwordly": "dev-master"
    }

After this, run `composer update`

## Usage

Passwordly is namespaced to `innoscience/passwordly/passwordly`, at the top of your file you'll want to use:

	use Innoscience\Passwordly\Passwordly;

### Creating passwords
When creating passwords, Passwordly first checks what the character requirements are. Any difference is made up with the addition of randomly cased alphanumeric characters.

#####Basic: generate random 8 character password, no symbols
	$password = Password::can()->generate(8);

#####Password with 1 - 3 symbols and 8 - 16 characters in length
	$password = Password::can()->hasSymbols(1,3)->generate(8, 16);

#####Password with at least 1 uppercase, lowercase and numeric character and 8 characters in length
	$password = Password::can()->hasLower(1)->hasNumbers(1)->hasUpper(1)->generate(8);

### Checking password

#####Check that password has at least 1 number and upper case letter and is at least 8 characters in length
	$isOk = Password::can()->hasNumbers(1)->hasUpper(1)->hasLength(8)->check($password);

#####Check that password has no more than 1 to 3 numbers, 1 symbol and upper case letter and is at least 8 but not more than 16 characters in length
	$isOk = Password::can()->hasNumbers(1,3)->hasUpper(1)->hasSymbols(1)->hasLength(8,16)->strict()->check($password);
When checking passwords, Passwordly does not use the maximum argument unless the `->strict()` method is invoked.

#####Check password and get errors
	$passwordCheck = new Passwordly();
	$isOk = $passwordCheck->hasNumbers(1)->hasUpper(1)->check($password);
	$errors = $passwordCheck->errors(); // # Returns array with each failed requirement



## Details

### Instantiating 

Passwordly can be instantiated either via the `Passwordly::can()` static constructor or simply by calling `new Passwordly`. Reading the error messages requires access to the Passwordly instance, so chaining calls off of `Passwordly::can()` is not recommended for those use-cases.

### Chainable Methods

* `hasLower($min, $max = null)` : The min/max number of lower-case characters that can be in a password.
* `hasUpper($min, $max = null)` : The min/max number of upper-case characters that can be in a password.
* `hasNumbers($min, $max = null)` : The min/max number of numeric characters that can be in a password.
* `hasSymbols($min, $max = null)` : The min/max number of non-alphanumeric characters that can be in a password.
* `hasSpaces($min, $max = null)` : The min/max number of spaces that can be in a password.
* `hasLength($min, $max = null)` : The min/max length of the password.
* `strict()` : Tells `check()` to also enforce the `$max` argument for all chainable methods

### Final Methods

* `generate($min, $max = null)` : Generate a password of $min/$max size from the set requirements, returns a `string`
* `check($password)` : Check the password against the set requirements, returns `true` or `false` 

### Other Methods
* `errors()` : Returns an `array` of errors resulting from a `->check()`

### Utility Methods
These utility methods affect all instances of Passwordly when used.

* `Passwordly::setLowerPool($characterPool)` : Override the lower-case character pool
* `Passwordly::setUpperPool($characterPool)` : Override the upper-case character pool
* `Passwordly::setNumberPool($characterPool)` : Override the numeric character pool
* `Passwordly::setSymbolPool($characterPool)` : Override the symbol character pool
* `Passwordly::getLowerPool()` : Get the lower-case character pool
* `Passwordly::getUpperPool()` : Get the upper-case character pool
* `Passwordly::getNumberPool()` : Get the numeric character pool
* `Passwordly::getSymbolPool()` : Get the symbol character pool
* `Passwordly::setDisableOpenssl($bool)` : Passwordly uses the `openssl_random_pseudo_bytes()` function in php to generate random strings, if this is unavailable use this method and set it to `TRUE` to use a built-in alternative function.

### Tests
Passwordly is fully unit tested. Tests are located in the `tests` directory of the Passwordly package and can be run with `phpunit` in the package's base directory.


## License
Passwordly is licensed under GPLv2