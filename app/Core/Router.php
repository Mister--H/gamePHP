<?php

namespace App\Core;

class Router {
    protected $routes = [];
    protected $middleware = [];
    protected $groupMiddleware = [];

    public function group($prefix, $callback, $middleware = []) {
        $this->groupMiddleware[$prefix] = $middleware;
        call_user_func($callback, $this); // Execute the callback, passing in this router instance
        unset($this->groupMiddleware[$prefix]); // Clear group middleware after callback execution
    }

    public function get($uri, $action, $middleware = []) {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post($uri, $action, $middleware = []) {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    protected function addRoute($method, $uri, $action, $middleware) {
        $pattern = preg_replace('#\{[\w]+\}#', '([^/]+)', $uri);
        foreach ($this->groupMiddleware as $groupPrefix => $groupMiddleware) {
            if (strpos($uri, $groupPrefix) === 0) { // If URI starts with group prefix
                $middleware = array_merge($middleware, $groupMiddleware);
                break;
            }
        }
        $this->routes[$method][$pattern] = $action;
        if (!empty($middleware)) {
            $this->middleware[$pattern] = $middleware;
        }
    }

    public function direct($uri, $requestType) {
        foreach ($this->routes[$requestType] as $pattern => $action) {
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Remove the full match from the beginning
                
                if (isset($this->middleware[$pattern])) {
                    foreach ($this->middleware[$pattern] as $middleware) {
                        if (!$middleware()) {
                            header('Location: /login');
                            exit;
                        }
                    }
                }
                
                return $this->callActionWithParams($action, $matches);
            }
        }

        throw new \Exception('No route defined for this URI.');
    }

    protected function callActionWithParams($action, $params) {
        list($controller, $method) = explode('@', $action);
        $controller = "App\\Controllers\\{$controller}";
        if (!class_exists($controller)) {
            throw new \Exception("Controller not found: {$controller}");
        }

        $controllerObject = new $controller();
        if (!method_exists($controllerObject, $method)) {
            throw new \Exception("{$controller} does not respond to the {$action} action.");
        }

        return call_user_func_array([$controllerObject, $method], $params);
    }
}