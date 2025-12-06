<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Tests;

use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeChallengeException;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeVerifierException;
use danielburger1337\OAuth2\PKCE\Exception\UnsupportedCodeChallengeMethodException;
use danielburger1337\OAuth2\PKCE\ProofKeyForCodeExchange;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProofKeyForCodeExchange::class)]
class ProofKeyForCodeExchangeTest extends TestCase
{
    private ProofKeyForCodeExchange $pkce;

    protected function setUp(): void
    {
        $this->pkce = new ProofKeyForCodeExchange();
    }

    /**
     * @param string[] $expected
     */
    #[Test]
    #[DataProvider('dataProivder_getSupportedCodeChallengeMethods')]
    public function getSupportedCodeChallengeMethodsReturnsExpected(ProofKeyForCodeExchange $pkce, array $expected): void
    {
        $returnValue = $pkce->getSupportedCodeChallengeMethods();

        $this->assertEquals($expected, $returnValue);
    }

    #[Test]
    public function verifyCodeChallengeInvalidlyFormattedVerifierThrowsException(): void
    {
        $this->expectException(InvalidCodeVerifierException::class);

        $this->pkce->verifyCodeChallenge('plain', 'abcdef', 'abcdef');
    }

    #[Test]
    public function verifyCodeChallengeUnsupportedMethodThrowsException(): void
    {
        $this->expectException(UnsupportedCodeChallengeMethodException::class);

        $this->pkce->verifyCodeChallenge('unsupported', 'abcdef', 'abcdef');
    }

    #[Test]
    #[DataProvider('dataProvider_verifyCodeChallenge')]
    public function verifyCodeChallengeReturnsExpected(string $method, string $challenge, string $verifier, bool $expected): void
    {
        $returnValue = $this->pkce->verifyCodeChallenge($method, $challenge, $verifier);

        $this->assertEquals($expected, $returnValue);
    }

    #[Test]
    public function createCodeChallengeUnsupportedMethodThrowsException(): void
    {
        $this->expectException(UnsupportedCodeChallengeMethodException::class);

        $this->pkce->createCodeChallenge('unsupported');
    }

    #[Test]
    public function createCodeChallengeCodeChallengeHasExpectedLength(): void
    {
        $pkce = new ProofKeyForCodeExchange([new PlainChallengeMethod()], 28);

        $returnValue = $pkce->createCodeChallenge('plain');

        $this->assertTrue(\strlen($returnValue->challenge) === 28 * 2);
    }

    #[Test]
    public function createCodeChallengePlainIsExpected(): void
    {
        $returnValue = $this->pkce->createCodeChallenge('plain');

        $this->assertEquals('plain', $returnValue->method);
        $this->assertEquals($returnValue->verifier, $returnValue->challenge);
    }

    #[Test]
    public function createCodeChallengeS256IsExpected(): void
    {
        $returnValue = $this->pkce->createCodeChallenge('S256');

        $this->assertEquals('S256', $returnValue->method);
        $this->assertNotEquals($returnValue->verifier, $returnValue->challenge);
    }

    #[Test]
    public function ensureCodeChallengeIsAllowedUnsupportedMethodThrowsException(): void
    {
        $this->expectException(UnsupportedCodeChallengeMethodException::class);

        $this->pkce->ensureCodeChallengeIsAllowed('unsupported', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz');
    }

    #[Test]
    public function ensureCodeChallengeIsAllowedInvalidCodeChallengeFormatThrowsException(): void
    {
        $this->expectException(InvalidCodeChallengeException::class);

        $this->pkce->ensureCodeChallengeIsAllowed('S256', 'abcdef');
    }

    #[Test]
    public function ensureCodeChallengeIsAllowedHasNoError(): void
    {
        $this->expectNotToPerformAssertions();

        $this->pkce->ensureCodeChallengeIsAllowed('S256', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz');
    }

    /**
     * @return array<array{0: ProofKeyForCodeExchange, 1: string[]}>
     */
    public static function dataProivder_getSupportedCodeChallengeMethods(): array
    {
        return [
            [new ProofKeyForCodeExchange(),  ['S256', 'plain']],
            [new ProofKeyForCodeExchange([new PlainChallengeMethod()]),  ['plain']],
            [new ProofKeyForCodeExchange([new S256ChallengeMethod()]),  ['S256']],
            [new ProofKeyForCodeExchange([new PlainChallengeMethod(), new S256ChallengeMethod()]),  ['plain', 'S256']],
        ];
    }

    /**
     * @return array<array{0: string, 1: string, 2: string, 3: bool}>
     */
    public static function dataProvider_verifyCodeChallenge(): array
    {
        return [
            ['S256', 'GdaOtJJqQ1lbOFDMWTm96Ss04V0DSzp5FjGg6cHgc80', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz', true],
            ['S256', 'invalid', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz', false],

            ['S256', 'dshwMd3H9tjQRewmXnkZBkc39-vHzyrIqmrppSrw4IE', 'wanfiwnafiwnr902nmf902mfien90812qjniwfanfiw', true],
            ['S256', 'otherInvalid', 'wanfiwnafiwnr902nmf902mfien90812qjniwfanfiw', false],

            ['plain', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz', 'abcdefefghijklmnopqrstuvwxyzabcdefefghijklmnopqrstuvwxyz', true],
            ['plain', 'otherOtherInvalid', 'wanfiwnafiwnr902nmf902mfien90812qjniwfanfiw', false],
        ];
    }
}
