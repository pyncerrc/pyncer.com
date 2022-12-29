<?php
use Pyncer\App\Middleware\DebugMiddleware;
use Pyncer\App\Middleware\I18nMiddleware;
use Pyncer\App\Middleware\Redirect\WwwMiddleware;
use Pyncer\App\Middleware\Redirect\HttpsMiddleware;
use Pyncer\App\Middleware\Response\RouterResponseMiddleware;
use Pyncer\App\Middleware\Routing\PageRouterMiddleware;
use Pyncer\Http\Message\Status;
use Pyncer\Docs\Identifier as ID;
use Pyncer\Docs\Middleware\InitializeMiddleware;
use Pyncer\Docs\Middleware\ThemeMiddleware;

use const DIRECTORY_SEPARATOR as DS;
use const Pyncer\Docs\DEBUG_ENABLED as DOCS_DEBUG_ENABLED;
use const Pyncer\Docs\HTTPS_ENABLED as DOCS_HTTPS_ENABLED;
use const Pyncer\Docs\HTTPS_FORCE as DOCS_HTTPS_FORCE;
use const Pyncer\Docs\I18N_LOCALE_CODES as DOCS_I18N_LOCALE_CODES;
use const Pyncer\Docs\I18N_DEFAULT_LOCALE_CODE as DOCS_I18N_DEFAULT_LOCALE_CODE;

error_reporting(-1);
ini_set("display_errors", 1);
ini_set('display_startup_errors', 1);

$app = require_once dirname(__DIR__) .
    DS . 'private' .
    DS . 'code' .
    DS . 'initialize.php';

$app->append(
    new DebugMiddleware(
        enabled: DOCS_DEBUG_ENABLED
    ),

    new WwwMiddleware(
        includeWww: false,
        redirectStatus: Status::REDIRECTION_301_MOVED_PERMANENTLY
    ),

    new HttpsMiddleware(
        enabled: DOCS_HTTPS_ENABLED,
        forceHttps: DOCS_HTTPS_FORCE
    ),

    new InitializeMiddleware(),

    new I18nMiddleware(
        sourceMapIdentifier: ID::I18N_SOURCE_MAP,
        localeCodes: DOCS_I18N_LOCALE_CODES,
        defaultLocaleCode: DOCS_I18N_DEFAULT_LOCALE_CODE,
        fallbackLocaleCode: DOCS_I18N_DEFAULT_LOCALE_CODE,
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
