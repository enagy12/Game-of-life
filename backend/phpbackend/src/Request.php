<?php namespace hu\doxasoft\phpbackend;

use Exception;

/**
 * Class Request
 *
 * @property array $routes
 * @property string $method
 * @property mixed $payload
 * @property int $routeIndex
 * @property int $routeCount
 */
class Request {
    private $routes;
    private $method;
    private $payload;
    private $routeIndex;
    private $routeCount;

    function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->createRoutes();
        $this->parsePayload();
    }

    public function getRoute() {
        return !empty($this->routes[$this->routeIndex]) ? $this->routes[$this->routeIndex] : null;
    }

    public function getNextRoute() {
        $this->routeIndex++;
        return $this->getRoute();
    }

    public function getPreviousRoute() {
        $this->routeIndex--;
        return $this->getRoute();
    }

    public function getMethod() {
        return $this->method;
    }

    public function isOptions() {
        return $this->method === 'OPTIONS';
    }

    public function isGet() {
        return $this->method === 'GET';
    }

    public function isPost() {
        return $this->method === 'POST';
    }

    /**
     * @param string $class
     * @return mixed
     */
    public function getPayload($class = null) {
        if (class_exists($class)) {
            return new $class($this->payload);
        }
        return $this->payload;
    }

    public function hasPayload() {
        return $this->payload != null;
    }

    public function getAuthorizationToken() {
        try {
            $headers = apache_request_headers();
            if (isset($headers['Authorization'])) {
                list($jwt) = sscanf($headers['Authorization'], 'Bearer %s');
                return $jwt;
            } else {
                return null;
            }
        } catch (Exception $e) {
            return null;
        }
    }

    private function parsePayload() {
        $this->parseJSON();
    }

    private function parseJSON() {
        try {
            $json = file_get_contents('php://input');
            if ($json !== null) {
                $this->payload = json_decode($json);
            }
        } catch (Exception $e) {
            $this->payload = null;
        }
    }

    private function createRoutes() {
        $this->routes = array();
        $base_url = $this->getCurrentUri();
        foreach (explode('/', $base_url) as $route) {
            if (trim($route) != '') {
                array_push($this->routes, $route);
            }
        }
        $this->routeIndex = -1;
        $this->routeCount = count($this->routes);
    }

    private function getCurrentUri() {
        $basePath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basePath));
        if (strstr($uri, '?')) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

}
