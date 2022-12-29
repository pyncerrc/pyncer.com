The bearer authenticator class authorizes a user using the [bearer authentication
scheme](https://datatracker.ietf.org/doc/html/rfc6750). The credentials are read from the [`Authorization`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Authorization)
header of a request.

```http
Authorization: Bearer <token>
```

The token can be any hash associated with a user that you can match against.

```http
Authorization: Bearer XYpXAuNn9hXnfHCTJaHhhc3sN7nYjKpQ
```

## Example

```php
use Psr\Http\Message\ServerRequestInterface;
use Pyncer\Access\BearerAuthenticator;
use Pyncer\Data\Mapper\MapperAdaptor;
use Pyncer\Http\Server\RequestHandlerInterface;
use Vendor\Site\Identifier as ID;
use Vendor\Site\Table\Token\TokenMapper;
use Vendor\Site\Table\User\UserMapper;

// $request: ServerRequestInterface
// $handler: RequestHandlerInterface

$connection = $handler->get(ID::DATABASE);

$tokenMapperAdaptor = new MapperAdaptor(
    mapper: new TokenMapper($connection),
);

$userMapperAdaptor = new MapperAdaptor(
    mapper: new UserMapper($connection),
);

$access = new BearerAuthenticator(
    tokenMapperAdaptor: $tokenMapperAdaptor,
    userMapperAdaptor: $userMapperAdaptor,
    request: $request,
    realm: 'my-app',
);

$response = $access->getResponse($handler);

if ($response !== null) {
    // WWW-Authenticate response
} elseif ($access->hasAuthenticated()) {
    // Authenticated
    var_dump($access->getUser());
} else {
    // No Authorization header
}
```
