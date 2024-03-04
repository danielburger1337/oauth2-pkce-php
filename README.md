[![PHPCSFixer](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpcsfixer.yml/badge.svg)](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpcsfixer.yml)
[![PHPStan](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpstan.yml/badge.svg)](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpstan.yml)
[![PHPUnit](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpunit.yml/badge.svg)](https://github.com/danielburger1337/oauth2-pkce-php/actions/workflows/phpunit.yml)
![Packagist Version](https://img.shields.io/packagist/v/danielburger1337/oauth2-pkce?link=https%3A%2F%2Fpackagist.org%2Fpackages%2Fdanielburger1337%2Foauth2-pkce)
![Packagist Downloads](https://img.shields.io/packagist/dt/danielburger1337/oauth2-pkce?link=https%3A%2F%2Fpackagist.org%2Fpackages%2Fdanielburger1337%2Foauth2-pkce)

# danielburger1337/oauth2-pkce

A PHP 8.2+ library that helps you both create and/or verify [OAuth2 PKCE](https://datatracker.ietf.org/doc/html/rfc7636) challenges.

## Install

This library is [PSR-4](https://www.php-fig.org/psr/psr-4/) compatible and can be installed via PHP's dependency manager [Composer](https://getcomposer.org).

```shell
composer require danielburger1337/oauth2-pkce
```

## Documentation

Todo

## Running Tests Locally

This library is fully unit tested. It also uses strict static analysis to minimize the possibility of unexpected runtime errors.

```sh
composer install

vendor/bin/php-cs-fixer fix
vendor/bin/phpstan
vendor/bin/phpunit
```

## License

This software is available under the [MIT](LICENSE) license.
