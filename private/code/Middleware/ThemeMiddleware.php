<?php
namespace Pyncer\Docs\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Docs\Identifier as ID;
use Pyncer\Docs\Component\Theme\PyncerTheme;
use Pyncer\Docs\Component\Theme\ThemeDecorator;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;

use const DIRECTORY_SEPARATOR as DS;

class ThemeMiddleware implements MiddlewareInterface
{
    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        $router = $handler->get(ID::ROUTER);

        $theme = new PyncerTheme($request);
        $decorator = new ThemeDecorator($theme);
        $router->setComponentDecorator($decorator);

        return $handler->next($request, $response);
    }
}
