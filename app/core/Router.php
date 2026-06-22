<?php

namespace App\Core;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $action)
    {
        // Convert URI to regex
        $uri = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $uri);
        $uri = '/^' . str_replace('/', '\/', $uri) . '$/i';

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public function get($uri, $action)
    {
        $this->add('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->add('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->add('PUT', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->add('DELETE', $uri, $action);
    }

    public function dispatch($uri, $method)
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Handle subdirectory
        // Get the directory where index.php is located, but relative to document root
        $basePath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $basePath = str_replace('/public', '', $basePath);
        
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        if (empty($uri)) $uri = '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['uri'], $uri, $matches)) {
                $action = $route['action'];

                if (is_callable($action)) {
                    return call_user_func_array($action, [$matches]);
                }

                if (is_string($action)) {
                    list($controller, $method) = explode('@', $action);
                    $controller = "App\\Controllers\\$controller";
                    $instance = new $controller();
                    
                    // Filter matches to get only named parameters
                    $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                    
                    return call_user_func_array([$instance, $method], [$params]);
                }
            }
        }

        $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        echo "Error $code: Page Not Found";
        exit;
    }
}
