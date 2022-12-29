The basic authenticator class authorizes a user using the [basic authentication
scheme](https://datatracker.ietf.org/doc/html/rfc7617). The credentials are read from the [`Authorization`](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Authorization)
header of a request.

```http
Authorization: Basic <credentials>
```

The credentials are constructed by combining the username and the password with a colon `username:password`, and then by encoding it in base64 `dXNlcm5hbWU6cGFzc3dvcmQ`.

```http
Authorization: Basic dXNlcm5hbWU6cGFzc3dvcmQ
```

## Example

```php
use Psr\Http\Message\ServerRequestInterface;
use Pyncer\Access\BasicAuthenticator;
use Pyncer\Data\Mapper\MapperAdaptor;
use Pyncer\Data\MapperQuery\FiltersQueryParam;
use Pyncer\Http\Server\RequestHandlerInterface;
use Vendor\Site\Identifier as ID;
use Vendor\Site\Table\User\UserMapper;
use Vendor\Site\Table\User\UserMapperQuery;

// $request: ServerRequestInterface
// $handler: RequestHandlerInterface

$connection = $handler->get(ID::DATABASE);

// Set up a mapper adaptor to select a user from the database using the
// credentials found in the authorization header. In this case the user
// database uses 'username' and 'password' as the field names so no key
// formatter is needed.
$userMapperQuery = new UserMapperQuery();
$userMapperQuery->setFilter(new FiltersQueryParam(
    'enabled eq true and deleted eq false'
));
$userMapperAdaptor = new MapperAdaptor(
    mapper: new UserMapper($connection),
    mapperQuery: $userMapperQuery,
);

$access = new BasicAuthenticator(
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
