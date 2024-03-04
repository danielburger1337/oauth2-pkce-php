<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Exception;

/**
 * @codeCoverageIgnore
 */
class UnsupportedCodeChallengeMethodException extends PkceException
{
    public function __construct(string $method)
    {
        parent::__construct(\sprintf('The code challenge method "%s" is not supported.', $method));
    }
}
