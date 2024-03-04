<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\CodeChallengeMethod;

interface CodeChallengeMethodInterface
{
    /**
     * The code challenge name.
     */
    public function getName(): string;

    /**
     * Create a code challenge from a code verifier.
     *
     * @param string $codeVerifier The code verifier to create the challenge from.
     */
    public function createCodeChallenge(string $codeVerifier): string;

    /**
     * Verify a PKCE code challenge.
     *
     * @param string $codeVerifier  The client supplied verifier.
     * @param string $codeChallenge The stored code challenge.
     */
    public function verifyCodeChallenge(string $codeVerifier, string $codeChallenge): bool;
}
