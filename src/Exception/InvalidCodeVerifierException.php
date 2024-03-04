<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Exception;

/**
 * @codeCoverageIgnore
 */
class InvalidCodeVerifierException extends PkceException
{
    public function __construct()
    {
        parent::__construct('The presented code verifier does not match the required format.');
    }
}
