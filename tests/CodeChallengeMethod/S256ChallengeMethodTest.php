<?php declare(strict_types=1);

namespace danielburger1337\OAuth2\PKCE\Tests\CodeChallengeMethod;

use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(S256ChallengeMethod::class)]
class S256ChallengeMethodTest extends TestCase
{
    private S256ChallengeMethod $method;

    protected function setUp(): void
    {
        $this->method = new S256ChallengeMethod();
    }

    #[Test]
    public function methodNameConstantReturnsS256(): void
    {
        $this->assertEquals('S256', S256ChallengeMethod::METHOD_NAME);
    }

    #[Test]
    public function getNameReturnsS256(): void
    {
        $returnValue = $this->method->getName();

        $this->assertEquals('S256', $returnValue);
    }

    #[Test]
    #[DataProvider('dataProvider_createCodeChallenge')]
    public function createCodeChallengeReturnsSha256Hash(string $codeVerifier, string $expected): void
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
            ['abcdef', 'vvV-x_U6bUC-tkCngKY5yDvCmsipgW8fxsXG3Nk8RyE'],
            ['16489fw4af8w1', '-3-lQb7X7iDSqD1MZ6hcf4ZIwhyXeSFNNW10wGr3yEY'],
            ['wfwafw92jfi3nmf', 'SD_BXF-7arqHUVebp7BupGRb5vW-M9vHENHH3FJzE8g'],
        ];
    }

    /**
     * @return array<array{0: string, 1: string, 2: bool}>
     */
    public static function dataProvider_verifyCodeChallenge(): array
    {
        return [
            ['abcdef', 'vvV-x_U6bUC-tkCngKY5yDvCmsipgW8fxsXG3Nk8RyE', true],
            ['abcdef', 'fedcba', false],
        ];
    }
}
