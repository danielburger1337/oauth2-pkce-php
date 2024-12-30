<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Model;

/**
 * @codeCoverageIgnore
 */
final class CodeChallenge
{
    /**
     * @param string $method    The "code_challenge_method".
     * @param string $challenge The "code_challenge".
     * @param string $verifier  The "code_verifier".
     */
    public function __construct(
        public readonly string $method,
        public readonly string $challenge,
        public readonly string $verifier,
    ) {
    }
}
