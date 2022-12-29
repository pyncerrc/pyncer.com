<?php
namespace Pyncer\Docs\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Docs\Identifier as ID;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Source\SourceMap;

use const DIRECTORY_SEPARATOR as DS;

class InitializeMiddleware implements MiddlewareInterface
{
    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        // I18n
        $routerSourceMap = new SourceMap();
        $routerSourceMap->add(
            'base',
            getcwd() . DS . 'private' . DS . 'locale'
        );

        $handler->set(ID::I18N_SOURCE_MAP, $routerSourceMap);

        // Router
        $routerSourceMap = new SourceMap();
        $routerSourceMap->add(
            'base',
            getcwd() . DS . 'private' . DS . 'page'
        );

        $handler->set(ID::ROUTER_SOURCE_MAP, $routerSourceMap);

        return $handler->next($request, $response);
    }
}
