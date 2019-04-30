<?php

use Symfony\Component\Console\Application;
use Snowdog\DevTest\Component\CommandRepository;

define('APP_BASE_DIR', __DIR__ . '/');

$container = require APP_BASE_DIR . 'app/bootstrap.php';

$app = new Application();

$commandRepository = CommandRepository::getInstance();
$commandRepository->applyCommands($app);

$app->run();