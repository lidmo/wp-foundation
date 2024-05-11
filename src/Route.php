<?php

namespace Lidmo\WP\Foundation;

use Psr\Container\ContainerInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    private $middlewares = [];
    private $prefix = 'web';
    private $name = '';
    private $registeredRoutes = [];
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get($route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'GET', $callback, $middlewares);
    }

    public function post($route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'POST', $callback, $middlewares);
    }

    public function put($route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'PUT', $callback, $middlewares);
    }

    public function patch($route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'PATCH', $callback, $middlewares);
    }

    public function delete(string $route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'DELETE', $callback, $middlewares);
    }

    public function match(array $methods, string $route, $callback, string|array $middlewares = [])
    {
        foreach ($methods as $method) {
            $this->registerRoute($route, strtoupper($method), $callback, $middlewares);
        }
    }

    public function prefix(string $prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function middleware(string|array $middleware): Route
    {
        if(is_string($middleware)) {
            $middleware = [$middleware];
        }
        array_map([$this, 'addMiddleware'], $middleware);
        return $this;
    }

    public function getRegisteredRoutes()
    {
        return $this->registeredRoutes;
    }

    private function registerRoute($route, $method, $callback, $middlewares)
    {
        $this->name = $route;
        // Registrar middlewares
        $this->middleware($middlewares);

        if($this->prefix === 'api'){
            // Registrar rotas da REST API (rest_api_init)
            add_action('rest_api_init', function () use ($route, $method, $callback) {
                register_rest_route('lidmo/v1', '/' . $route, array(
                    'methods' => $method,
                    'callback' => function ($data) use ($route, $callback) {
                        $request = $this->createServerRequest();
                        $this->handleMiddlewares($route, $request, $callback);
                    },
                ));
            });
        }else {
            // Registrar a rota padrão do WordPress
            add_action('init', function () use ($route, $method, $callback) {
                add_rewrite_rule('^' . $route . '/?$', 'index.php?custom_route=1', 'top');
            });

            add_action('template_redirect', function () use ($route, $method, $callback) {
                if (get_query_var('custom_route')) {
                    if ($_SERVER['REQUEST_METHOD'] === $method) {
                        $request = $this->createServerRequest();
                        $this->handleMiddlewares($route, $request, $callback);
                        exit();
                    }
                }
            });
        }

        // Registrar a rota
        $this->registeredRoutes[$this->prefix] = [
            'route' => $route,
            'method' => $method,
            'callback' => $callback,
        ];

        $this->prefix = 'web';
    }

    private function createServerRequest(): ServerRequestInterface
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parsedUri = parse_url($uri);
        $path = $parsedUri['path'];

        return new ServerRequest(
            $_SERVER['REQUEST_METHOD'],
            get_home_url($path)
        );
    }

    private function handleMiddlewares($route, $request, $callback)
    {
        // Captura de parâmetros da rota
        $routeParams = [];
        $routeParts = explode('/', $route);
        $requestUri = $request->getUri()->getPath();
        $requestParts = explode('/', $requestUri);

        foreach ($routeParts as $key => $part) {
            if (strpos($part, '{') !== false && strpos($part, '}') !== false) {
                $routeParams[trim($part, '{}')] = $requestParts[$key];
            }
        }

        $request = $request->withAttribute('route_params', $routeParams);

        foreach ($this->middlewares[$route] as $middleware) {
            $callback = function ($request) use ($callback, $middleware) {
                return $middleware->handle($request, $callback);
            };
        }

        if (is_callable($callback)) {
            $callback($request);
        } else {
            if (is_string($callback)) {
                $callback = explode('@', $callback);
            }
            list($class, $method) = $callback;
            if (class_exists($class)) {
                $controller = $this->container->make($class);
                if (method_exists($controller, $method)) {
                    $controller->$method($request);
                }
            }
        }
    }

    private function addMiddleware(string $middleware): void
    {
        $this->middlewares[$this->name][] = $middleware;
    }
}