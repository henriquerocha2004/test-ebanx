<?php

namespace Core;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Kernel implements HttpKernelInterface
{
    private ContainerInterface $container;
    private RouteCollection $routes;

    public function __construct()
    {
        $this->routes = new RouteCollection();
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
    {
        $context = new RequestContext();
        $context->fromRequest($request);
        $matcher = new UrlMatcher($this->routes, $context);

        try {
            $attributes = $matcher->match($request->getPathInfo());
            $controller = $attributes['controller'];
            $action = $attributes['action'];
            unset($attributes['controller']);
            unset($attributes['action']);
            unset($attributes['_route']);
            $controllerInstance = $this->container->get($controller);
            $response = $controllerInstance->$action($request, ...$attributes);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    public function addRoute(string $path, array $resource, array $methods = []): void
    {
        $this->routes->add($path, (new Route(
            $path,
            [
                'controller' => $resource['controller'],
                'action' => $resource['action']
            ]
        ))->setMethods($methods));
    }
}