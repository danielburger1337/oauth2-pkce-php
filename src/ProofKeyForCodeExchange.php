<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE;

use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\CodeChallengeMethodInterface;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeChallengeException;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeVerifierException;
use danielburger1337\OAuth2\PKCE\Exception\UnsupportedCodeChallengeMethodException;
use danielburger1337\OAuth2\PKCE\Model\CodeChallenge;

class ProofKeyForCodeExchange
{
    /** @see https://tools.ietf.org/html/rfc7636#section-4.1 */
    final public const string ABNF_CODE_VERIFIER = '/^[A-Za-z0-9-._~]{43,128}$/';

    /** @see https://tools.ietf.org/html/rfc7636#section-4.2 */
    final public const string ABNF_CODE_CHALLENGE = '/^[A-Za-z0-9-._~]{43,128}$/';

    /** @var array<string, CodeChallengeMethodInterface> */
    private readonly array $challengeMethods;

    /**
     * @param iterable<CodeChallengeMethodInterface> $challengeMethods [optional] The supported code challenge methods.
     * @param int<22,64>                             $challengeLength  [optional] The number of bytes that are randomly generated.
     *                                                                 ASCII length of the created challenge will be n*2.
     */
    public function __construct(
        iterable $challengeMethods = [
            new S256ChallengeMethod(),
            new PlainChallengeMethod(),
        ],
        private readonly int $challengeLength = 32
    ) {
        $map = [];
        foreach ($challengeMethods as $method) {
            $map[$method->getName()] = $method;
        }
        $this->challengeMethods = $map;
    }

    /**
     * Gets the name of the supported code challenge methods.
     *
     * @return string[]
     */
    public function getSupportedCodeChallengeMethods(): array
    {
        return \array_keys($this->challengeMethods);
    }

    /**
     * Create a PKCE code challenge.
     *
     * @param string $method The code challenge method to use.
     *
     * @return CodeChallenge The create code challenge.
     *
     * @throws UnsupportedCodeChallengeMethodException If the given code challenge method is not supported.
     */
    public function createCodeChallenge(string $method): CodeChallenge
    {
        $method = $this->challengeMethods[$method] ?? throw new UnsupportedCodeChallengeMethodException($method);

        $codeVerifier = \bin2hex(\random_bytes($this->challengeLength));

        return new CodeChallenge(
            $method->getName(),
            $method->createCodeChallenge($codeVerifier),
            $codeVerifier
        );
    }

    /**
     * Verify a PKCE code challenge.
     *
     * This method is for **AUTHORIZATION SERVERS**.
     *
     * @param string $method    The "code_challenge_method" that was used to create the "code_challenge".
     * @param string $challenge The "code_challenge" to verify.
     * @param string $verifier  The "code_verifier" to verify against the "code_challenge".
     *
     * @return bool Returns `true` if the code challenge is valid, `false` otherwise.
     */
    public function verifyCodeChallenge(string $method, string $challenge, string $verifier): bool
    {
        $method = $this->challengeMethods[$method] ?? throw new UnsupportedCodeChallengeMethodException($method);

        if (\preg_match(self::ABNF_CODE_VERIFIER, $verifier) !== 1) {
            throw new InvalidCodeVerifierException();
        }

        return $method->verifyCodeChallenge($verifier, $challenge);
    }

    /**
     * Ensure that the given code challenge method and code challenge are supported.
     *
     * This method is for **AUTHORIZATION SERVERS**.
     *
     * @param string $method        The "code_challenge_method" to check.
     * @param string $codeChallenge The "code_challenge" to check.
     *
     * @throws UnsupportedCodeChallengeMethodException If the "code_challenge_method" is unsupported.
     * @throws InvalidCodeChallengeException           If the "code_challenge" does not match the required format.
     */
    public function ensureCodeChallengeIsAllowed(string $method, string $codeChallenge): void
    {
        if (!\array_key_exists($method, $this->challengeMethods)) {
            throw new UnsupportedCodeChallengeMethodException($method);
        }

        if (\preg_match(self::ABNF_CODE_CHALLENGE, $codeChallenge) !== 1) {
            throw new InvalidCodeChallengeException();
        }
    }
}
