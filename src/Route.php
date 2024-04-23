<?php

namespace Lidmo\WP\Foundation;

use Psr\Container\ContainerInterface;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ServerRequestInterface;

class Route
{
    private $middlewares = [];
    private $prefix = '';
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

    public function delete($route, $callback, $middlewares = [])
    {
        $this->registerRoute($route, 'DELETE', $callback, $middlewares);
    }

    public function match($methods, $route, $callback, $middlewares = [])
    {
        foreach ((array)$methods as $method) {
            $this->registerRoute($route, strtoupper($method), $callback, $middlewares);
        }
    }

    public function prefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function name($name)
    {
        $this->name = $name;
    }

    public function middleware($middlewares)
    {
        $this->middlewares[$this->prefix . $this->name] = $middlewares;
    }

    public function getRegisteredRoutes()
    {
        return $this->registeredRoutes;
    }

    private function registerRoute($route, $method, $callback, $middlewares)
    {
        $route = $this->prefix . $route;

        // Registrar middlewares
        $this->middlewares[$route] = $middlewares;

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

        // Registrar ação de administração (admin_post)
        add_action('admin_post_' . $route, function () use ($callback) {
            $request = $this->createServerRequest();
            $this->handleMiddlewares($route, $request, $callback);
        });

        // Registrar requisições AJAX (wp_ajax_)
        add_action('wp_ajax_' . $route, function () use ($callback) {
            $request = $this->createServerRequest();
            $this->handleMiddlewares($route, $request, $callback);
        });

        // Registrar rotas da REST API (rest_api_init)
        add_action('rest_api_init', function () use ($route, $method, $callback) {
            register_rest_route('custom-route-namespace/v1', '/' . $route, array(
                'methods' => $method,
                'callback' => function ($data) use ($route, $callback) {
                    $request = $this->createServerRequest();
                    $this->handleMiddlewares($route, $request, $callback);
                },
            ));
        });

        // Registrar a rota
        $this->registeredRoutes[] = [
            'name' => $this->name,
            'prefix' => $this->prefix,
            'route' => $route,
            'method' => $method,
            'callback' => $callback,
        ];
    }

    private function createServerRequest(): ServerRequestInterface
    {
        $uri = $_SERVER['REQUEST_URI'];
        $parsedUri = parse_url($uri);
        $path = $parsedUri['path'];

        return new ServerRequest(
            $_SERVER['REQUEST_METHOD'],
            'http://localhost' . $path
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
}