<?php
class Router
{
    private $routes = [];
    private $response;
    private $groupPrefix = '';
    private $groupMiddlewares = [];

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function group($prefix, $middlewares, $callback)
    {
        $previousGroupPrefix = $this->groupPrefix;
        $previousGroupMiddlewares = $this->groupMiddlewares;

        $this->groupPrefix = $previousGroupPrefix . '/' . trim($prefix, '/');
        $this->groupMiddlewares = array_merge($previousGroupMiddlewares, $middlewares);

        $callback($this);

        $this->groupPrefix = $previousGroupPrefix;
        $this->groupMiddlewares = $previousGroupMiddlewares;
    }

    public function add($uri, $method, $controller, $action, $middlewares = [])
    {
        $uri = trim($this->groupPrefix . '/' . trim($uri, '/'), '/');
        $middlewares = array_merge($this->groupMiddlewares, $middlewares);
        $this->routes[] = [
            'uri' => $uri,
            'method' => $method,
            'controller' => $controller,
            'action' => $action,
            'middlewares' => $middlewares,
        ];
    }

    public function get($uri, $controller, $action, $middlewares = [])
    {
        $this->add($uri, 'GET', $controller, $action, $middlewares);
    }

    public function post($uri, $controller, $action, $middlewares = [])
    {
        $this->add($uri, 'POST', $controller, $action, $middlewares);
    }

    public function patch($uri, $controller, $action, $middlewares = [])
    {
        $this->add($uri, 'PATCH', $controller, $action, $middlewares);
    }

    public function put($uri, $controller, $action, $middlewares = [])
    {
        $this->add($uri, 'PUT', $controller, $action, $middlewares);
    }

    public function delete($uri, $controller, $action, $middlewares = [])
    {
        $this->add($uri, 'DELETE', $controller, $action, $middlewares);
    }

    public function dispatch($uri, $method)
    {
        // 檢查隱藏字段 _method 並覆蓋請求方法
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        foreach ($this->routes as $route) {
            $routePattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $route['uri']);
            $routePattern = str_replace('/', '\/', $routePattern);
            if (preg_match('/^' . $routePattern . '$/', $uri, $matches) && $method === $route['method']) {
                array_shift($matches);
                $request = new Request();

                foreach ($route['middlewares'] as $middleware) {
                    $middlewareInstance = new $middleware($this->response);
                    $request = $middlewareInstance->handle($request);
                    if ($request === false) {
                        return; // 如果 Middleware 返回 false，則中止調用 Controller
                    }
                }

                $controller = new $route['controller']($this->response);
                call_user_func_array([$controller, $route['action']], array_merge([$request], $matches));
                return;
            }
        }
        $this->defaultResponse();
    }

    private function defaultResponse()
    {
        $this->response->json(['status' => 'error', 'message' => 'Page Not Found'], 404);
    }
}
