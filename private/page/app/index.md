The [pyncer/app](https://github.com/pyncerrc/pyncer-app) package is the main class that handles a request and outputs a response.

A series of middlewares can be passed to it to generate said response.

## Installation

Install via [Composer](https://getcomposer.org):

```bash
$ composer require pyncer/app
```

## Example

```php
use Pyncer\App\App;
use Pyncer\App\Middleware\DebugMiddleware;
use Pyncer\App\Middleware\I18nMiddleware;
use Pyncer\App\Middleware\Redirect\HttpsMiddleware;
use Pyncer\App\Middleware\Redirect\WwwMiddleware;
use Pyncer\App\Middleware\Response\RouterResponseMiddleware;
use Pyncer\App\Middleware\Routing\PageRouterMiddleware;
use Pyncer\Http\Message\Status;
use Vendor\Site\Identifier as ID;
use Vendor\Site\Middleware\InitializeMiddleware;
use Vendor\Site\Middleware\ThemeMiddleware;

$app = new App();

$app->append(
    new DebugMiddleware(
        enabled: false,
    ),

    new WwwMiddleware(
        includeWww: false,
        redirectStatus: Status::REDIRECTION_301_MOVED_PERMANENTLY,
    ),

    new HttpsMiddleware(
        enabled: true,
        forceHttps: true,
    ),

    new InitializeMiddleware(),

    new I18nMiddleware(
        sourceMapIdentifier: ID::I18N_SOURCE_MAP,
        localeCodes: ['en', 'fr', 'ru', 'ja'],
        defaultLocaleCode: 'en',
        fallbackLocaleCode: 'en',
    ),

    new PageRouterMiddleware(
        sourceMapIdentifier: ID::ROUTER_SOURCE_MAP,
        basePath: '',
        enableI18n: true,
        enableRewriting: true,
        enableRedirects: true,
    ),

    new ThemeMiddleware(),

    new RouterResponseMiddleware(),
);

$app->send();
```
