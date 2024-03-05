<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\CodeChallengeMethod;

final class PlainChallengeMethod implements CodeChallengeMethodInterface
{
    public const METHOD_NAME = 'plain';

    public function getName(): string
    {
        return self::METHOD_NAME;
    }

    public function createCodeChallenge(string $codeVerifier): string
    {
        return $codeVerifier;
    }

    public function verifyCodeChallenge(string $codeVerifier, string $codeChallenge): bool
    {
        return \hash_equals($codeChallenge, $codeVerifier);
    }
}
