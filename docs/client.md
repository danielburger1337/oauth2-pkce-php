## Usage as Client

As the client, the PKCE extension requires you to create a code challenge and to send that to the authorization server via the `code_challenge` parameter.

The code challenge is a pseudo-random sequence of bytes that you will create.
With every challenge, a code challenge verifier is created as well.

Depending on the code challenge method used, the verifier and the code challenge are cryptographically bound to each other. Because it is nearly impossible to recompute the code verifier from a code challenge, the authorization can be reasonablly sure that the client that requested the authorization code is the one trying to exchange it for an access token.

## Requesting an Authorization Code

When creating the authorization request (i.e. redirecting the user to the authorization endpoint or creating a [PAR](https://datatracker.ietf.org/doc/html/rfc9126) request), you have to include the `code_challenge` and `code_challenge_method` parameter. Similiar to the `state` parameter, you will have to store the created `code_verifier` somewhere (e.g. the users session) for later usage (when exchanging the issued authorization code for an access token).

You **MUST** not send the created `code_verifier` to the authorization server in this step!

```php
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\PlainChallengeMethod;
use danielburger1337\OAuth2\PKCE\CodeChallengeMethod\S256ChallengeMethod;
use danielburger1337\OAuth2\PKCE\Exception\UnsupportedCodeChallengeMethodException;
use danielburger1337\OAuth2\PKCE\ProofKeyForCodeExchange;

// params that will be added to the authorization URL
$queryParams = ['client_id' => 'abc'];

// options that will be stored before redirecting to the
// authorization URL (e.g. state, nonce, etc.)
$storedOptions = [];

$pkce = new ProofKeyForCodeExchange([
    new PlainChallengeMethod(),
    new S256ChallengeMethod(),
]);

try {
    $codeChallenge = $pkce->createCodeChallenge('S256');
} catch (UnsupportedCodeChallengeMethodException $e) {
    throw $e;
}

$queryParams['code_challenge_method'] = $codeChallenge->method;
$queryParams['code_challenge'] = $codeChallenge->challenge;

$storedOptions['code_verifier'] = $codeChallenge->verifier;

// save options to session
$request->getSession()->save($storedOptions);

$authorizationUrl = 'https://op.example.com/authorize';

// redirect user to authorizaiton endpoint
\header('Location: '.$authorizationUrl.'?'.\http_build_query($queryParams, encoding_type: \PHP_QUERY_RFC3986));
exit;
```

## Exchange the Authorization Code

The authorization server will have stored the information that you provided in the authorization request and only allow you to exchange the authorization code when you provide the `code_verifier` that matches the `code_challenge` in the token request.

```php
// options that you stored before redirecting to the
// authorization URL (e.g. state, nonce, etc.)
$storedOptions = $request->getSession()->get('oauth2_options');

$request = $requestFactory->createRequest('POST', 'https://op.example.com/oauth2/token');
$request->getBody()->write(\http_build_query([
    'client_id' => 'clientId',
    'client_secret' => 'clientSecret',
    'grant_type' => 'authorization_code',
    'code' => 'your authorization code',
    'code_verifier' => $storedOptions['code_verifier'],
], encoding_type: \PHP_QUERY_RFC3986));

$response = $httpClient->sendRequest($request);

// do your logic
```
