<?php

namespace App\Core;

class Router
{
    protected $wildCards = [
        'int' => '/^[0-9]+$/'
    ];

    /**
     * All registered routes.
     *
     * @var array
     */
    public $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * Load a user's routes file.
     *
     * @param string $file
     * @return Router
     */
    public static function load($file)
    {
        $router = new static;

        require $file;

        return $router;
    }

    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param string $controller
     */
    public function get($uri, $controller)
    {
        $this->routes['GET'][$uri] = $controller;
    }

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param string $controller
     */
    public function post($uri, $controller)
    {
        $this->routes['POST'][$uri] = $controller;
    }

    /**
     * Load the requested URI's associated controller method.
     *
     * @param string $uri
     * @param string $requestType
     * @return mixed
     */
    public function direct($uri, $requestType)
    {
        $matches = $this->getDetailsFromUrl($uri, $requestType);
//        var_dump($matches);die;

        if (array_key_exists(rawurldecode($uri), $this->routes[$requestType]) || ! is_null($matches['wild_card'])) {
            return $this->callAction(
                $wildCard = $matches['wild_card'],
                ...explode('@', $this->routes[$requestType][$wildCard ? $matches['route'] : $uri])
            );
        }

        throw new Exception('No route defined for this URI.');
    }

    /**
     * Load and call the relevant controller action.
     *
     * @param string $controller
     * @param string $action
     * @return mixed
     */
    protected function callAction($wildCard, $controller, $action)
    {
        $controller = "App\\Controllers\\{$controller}";
        $controller = new $controller;

        if (! method_exists($controller, $action)) {
            throw new Exception(
                "{$controller} does not respond to the {$action} action."
            );
        }

        return $wildCard ? $controller->$action($wildCard) : $controller->$action();
    }

    protected function getDetailsFromUrl(string $uri, string $requestType)
    {
        $routes = $this->routes[$requestType];
        $explodeUri = explode('/', $uri);

        foreach ($routes as $key => $value) {
            $explodeRoute = explode('/', $key);

            if (count($explodeUri) == count($explodeRoute)) {
                $diff = array_values(array_diff($explodeUri, $explodeRoute));

                if (! empty($diff))
                    foreach ($this->wildCards as $wildCard) {
                        if (preg_match($wildCard, $diff[0]))
                            return [
                                'route' => $key,
                                'wild_card' => $diff[0]
                            ];
                    }
            }
        }

        return [
            'route' => $uri,
            'wild_card' => null
        ];
    }
}
