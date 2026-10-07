<?php
/**
 * Microfy
 * Router.php
 * v0.1.3 
 * Author: SirCode
 */
class Router {
  protected array $routes = [ 'GET'=>[], 'POST'=>[], 'PUT'=>[], 'DELETE'=>[] ];

  public function get(string $path, callable $h): void {
    $this->routes['GET'][$path] = $h;
  }
  public function post(string $path, callable $h): void {
    $this->routes['POST'][$path] = $h;
  }

  public function dispatch(): void {
    $method     = $_SERVER['REQUEST_METHOD'];
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $base       = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $path       = ($base && strpos($requestUri, $base)===0)
                ? substr($requestUri, strlen($base))
                : $requestUri;
    $uri        = rtrim($path, '/') ?: '/';
    $routes     = $this->routes[$method] ?? [];

    if (isset($routes[$uri])) {
      echo ($routes[$uri])();
      return;
    }
    foreach ($routes as $route => $h) {
      $pattern = '#^'.preg_replace('#\{[^}]+\}#','([^/]+)', $route).'$#';
      if (preg_match($pattern, $uri, $m)) {
        array_shift($m);
        echo call_user_func_array($h, $m);
        return;
      }
    }
    http_response_code(404);
    echo '404 Not Found';
  }
}
