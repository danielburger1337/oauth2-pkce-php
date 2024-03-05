# Usage as Authorization Server

The authorization server verifies the code challenge.

## Accepting a Code Challenge during the authorization request

During the authorization request, the authorization server must verify that the `code_challenge_method` is supported and that the `code_challenge` matches the [required format](https://tools.ietf.org/html/rfc7636#section-4.2).

The two parameters are then associated with the authorization code and persited (most commonly alongside the authorization code in the database).

```php
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeChallengeException;
use danielburger1337\OAuth2\PKCE\Exception\UnsupportedCodeChallengeMethodException;
use danielburger1337\OAuth2\PKCE\ProofKeyForCodeExchange;

// the OAuth2 parameters from the query params / request object / request body
$parameters = [];

if (array_key_exists('code_challenge', $parameters)) {
    $pkce = new ProofKeyForCodeExchange([
        new PlainChallengeMethod(),
        new S256ChallengeMethod(),
    ]);

    // the used challenge method,
    // default to S256 if not provided by client explicitly
    $method = (string) ($parameters['code_challenge_method'] ?? 'S256');

    try {
        $pkce->ensureCodeChallengeIsAllowed($method, (string) $parameters['code_challenge']);
    } catch (UnsupportedCodeChallengeMethodException $e) {
        // the provided code_challenge_method is unsupported
        throw $e;
    } catch (InvalidCodeChallengeException $e) {
        // the provided code_challenge does not match the required format
        throw $e;
    }

    // don't forget to associate the method and challenge with the authorization code
    // you will need them when issiueing the access token from the authorization code
}
```

## Exchanging the Authorization Code for an Access Token

When the client now presents the authorization code at the `token_endpoint`, the authorization server **MUST** ensure that the provided `code_verifier` (depending on the challenge method) cryptographically matches the `code_challenge` from the authorization request.

```php
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use danielburger1337\OAuth2\PKCE\Exception\InvalidCodeVerifierException;
use danielburger1337\OAuth2\PKCE\Exception\UnsupportedCodeChallengeMethodException;
use danielburger1337\OAuth2\PKCE\ProofKeyForCodeExchange;

// the OAuth2 request parameters
$parameters = [];

// the OAuth2 authorization request data that belongs to the presented authorization code
$authRequest = [];

if (array_key_exists('code_challenge', $authRequest)) {
    $pkce = new ProofKeyForCodeExchange([
        new PlainChallengeMethod(),
        new S256ChallengeMethod(),
    ]);

    // the used challenge method,
    // default to S256 if it wasn't provided by client explicitly
    $method = (string) ($authRequest['code_challenge_method'] ?? 'S256');

    try {
        $isValid = $pkce->verifyCodeChallenge($method, (string) $authRequest['code_challenge'], (string) $parameters['code_verifier']);
    } catch (UnsupportedCodeChallengeMethodException $e) {
        // the stored code_challenge_method is unsupported
        throw $e;
    } catch (InvalidCodeVerifierException $e) {
        // the provided code_verifier does not match the required format
        throw $e;
    }

    if (!$isValid) {
        throw new \Exception('The PKCE verification failed because the provided code_verifier does not match the stored code_challenge');
    }
}
```
