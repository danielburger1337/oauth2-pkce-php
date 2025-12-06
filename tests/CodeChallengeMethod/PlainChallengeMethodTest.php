<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Tests\CodeChallengeMethod;

use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PlainChallengeMethod::class)]
class PlainChallengeMethodTest extends TestCase
{
    private PlainChallengeMethod $method;

    protected function setUp(): void
    {
        $this->method = new PlainChallengeMethod();
    }

    #[Test]
    public function methodNameConstantReturnsPlain(): void
    {
        $this->assertEquals('plain', PlainChallengeMethod::METHOD_NAME);
    }

    #[Test]
    public function getNameReturnsPlain(): void
    {
        $returnValue = $this->method->getName();

        $this->assertEquals('plain', $returnValue);
    }

    #[Test]
    #[DataProvider('dataProvider_createCodeChallenge')]
    public function createCodeChallengeReturnsWithoutModification(string $codeVerifier, string $expected): void
    {
        $returnValue = $this->method->createCodeChallenge($codeVerifier);

        $this->assertEquals($expected, $returnValue);
    }

    #[Test]
    #[DataProvider('dataProvider_verifyCodeChallenge')]
    public function verifyCodeChallengeReturnsExpected(string $codeVerifier, string $codeChallenge, bool $expected): void
    {
        $returnValue = $this->method->verifyCodeChallenge($codeVerifier, $codeChallenge);

        $this->assertEquals($expected, $returnValue);
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    public static function dataProvider_createCodeChallenge(): array
    {
        return [
            ['abcdef', 'abcdef'],
            ['16489fw4af8w1', '16489fw4af8w1'],
            ['wfwafw92jfi3nmf', 'wfwafw92jfi3nmf'],
        ];
    }

    /**
     * @return array<array{0: string, 1: string, 2: bool}>
     */
    public static function dataProvider_verifyCodeChallenge(): array
    {
        return [
            ['abcdef', 'abcdef', true],
            ['abcdef', 'fedcba', false],
        ];
    }
}
