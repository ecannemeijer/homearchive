<?php

namespace App;

class Router
{
    private $routes = [];
    private $current_route;

    /**
     * Definieer GET route
     */
    public function get($path, $callback)
    {
        $this->routes['GET'][$path] = $callback;
        return $this;
    }

    /**
     * Definieer POST route
     */
    public function post($path, $callback)
    {
        $this->routes['POST'][$path] = $callback;
        return $this;
    }

    /**
     * Route alle requests
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = $this->parse_path($_SERVER['REQUEST_URI']);
        
        // Controleer exacte match
        if (isset($this->routes[$method][$path])) {
            $this->execute_route($this->routes[$method][$path]);
            return;
        }
        
        // Controleer parameter routes
        foreach ($this->routes[$method] ?? [] as $route => $callback) {
            $pattern = $this->route_to_regex($route);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                $this->execute_route($callback, $matches);
                return;
            }
        }
        
        // 404
        http_response_code(404);
        echo $this->view('404');
        exit;
    }

    /**
     * Parse request path
     */
    private function parse_path($uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Get base path from APP_URL
        $app_url = config('app.url', 'http://localhost');
        $parsed_url = parse_url($app_url);
        $base = $parsed_url['path'] ?? '';
        
        // Remove base path if present
        if (!empty($base) && $base !== '/' && strpos($path, $base) === 0) {
            $path = substr($path, strlen($base));
        }
        
        return '/' . ltrim($path, '/');
    }

    /**
     * Zet route naar regex
     */
    private function route_to_regex($route)
    {
        $pattern = str_replace('/', '\/', $route);
        $pattern = preg_replace('/\{(\w+)\}/', '([^\/]+)', $pattern);
        return '/^' . $pattern . '$/';
    }

    /**
     * Voer route callback uit
     */
    private function execute_route($callback, $params = [])
    {
        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            $controller_class = '\\App\\Controllers\\' . $controller;
            
            if (!class_exists($controller_class)) {
                die('Controller niet gevonden: ' . $controller_class);
            }
            
            $instance = new $controller_class;
            if (!method_exists($instance, $method)) {
                die('Method niet gevonden: ' . $method);
            }
            
            call_user_func_array([$instance, $method], $params);
        } elseif (is_callable($callback)) {
            call_user_func_array($callback, $params);
        }
    }

    /**
     * Simple 404 view
     */
    private function view($view)
    {
        if ($view === '404') {
            return '<html><body style="font-family:Arial"><h1>404</h1><p>Pagina niet gevonden</p></body></html>';
        }
    }
}
