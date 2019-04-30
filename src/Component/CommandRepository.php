<?php

namespace Snowdog\DevTest\Component;

use Symfony\Component\Console\Application;

class CommandRepository
{
    private static $instance = null;

    private $commands = [];

    /**
     * @return CommandRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function registerCommand($className)
    {
        $instance = self::getInstance();
        $instance->addCommand($className);
    }

    public function applyCommands(Application $app)
    {
        if (empty($this->commands)) {
            return;
        }

        foreach ($this->commands as $class)
        {
            $app->add(new $class);
        }
    }

    private function addCommand($className)
    {
        $this->commands[] = $className;
    }
}