<?php

error_reporting(E_ALL);

use Snowdog\DevTest\Component\Menu;
use Snowdog\DevTest\Component\RouteRepository;
use Snowdog\DevTest\Exception\AccessException;

/**
 * App base directory path
 */
define('APP_BASE_DIR', __DIR__ . '/../');

/**
 * App debugging status
 */
define('APP_DEBUG_MODE', true);

/**
 * Start session
 */
session_start();

/**
 * Start output buffering
 */
ob_start();

try {
    $container = require APP_BASE_DIR . 'app/bootstrap.php';

    $routeRepository = RouteRepository::getInstance();

    $dispatcher = \FastRoute\simpleDispatcher($routeRepository);

    Menu::setContainer($container);

    $route = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

    switch ($route[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            header("HTTP/1.0 404 Not Found");
            require APP_BASE_DIR . 'src/view/404.phtml';
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            header("HTTP/1.0 405 Method Not Allowed");
            require APP_BASE_DIR . '/src/view/405.phtml';
            break;
        case FastRoute\Dispatcher::FOUND:
            $controller = $route[1];
            $parameters = $route[2];
            $container->call($controller, $parameters);
            ob_end_flush();
            break;
    }
} catch (AccessException $ex) {
    /** clean output buffer */
    ob_end_clean();

    if ($ex->getCode() === 403) {

        /** show error page */
        require APP_BASE_DIR . 'src/view/403.phtml';
        return;
    }

    /** redirect on login page */
    header('Location: /login');
} catch (Exception $ex) {
    /** clean output buffer */
    ob_end_clean();

    /** prepare and log error message into file */
    $error = $ex->getMessage() . ' - ' . $ex->getFile() . ' - ' . $ex->getLine();
    Snowdog\DevTest\Helper\Log::error($error);

    /** show error page */
    require APP_BASE_DIR . 'src/view/error.phtml';
}
