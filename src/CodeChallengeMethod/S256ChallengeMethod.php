<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\CodeChallengeMethod;

use Base64Url\Base64Url;

final class S256ChallengeMethod implements CodeChallengeMethodInterface
{
    public const METHOD_NAME = 'S256';

    public function getName(): string
    {
        return self::METHOD_NAME;
    }

    public function createCodeChallenge(string $codeVerifier): string
    {
        return Base64Url::encode(\hash('sha256', $codeVerifier, true));
    }

    public function verifyCodeChallenge(string $codeVerifier, string $codeChallenge): bool
    {
        return \hash_equals(
            $codeChallenge,
            $this->createCodeChallenge($codeVerifier)
        );
    }
}
