<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\User;
use DI\ContainerBuilder;
use Snowdog\DevTest\Exception\AccessException;

class BaseController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param bool $throwException
     * @return bool
     * @throws AccessException
     */
    public function onlyAuthorized($throwException = true)
    {
        $container = ContainerBuilder::buildDevContainer();
        $userManager = $container->get(UserManager::class);
        $user = null;

        if ( ! isset($_SESSION['login']) || empty($user = $userManager->getByLogin($_SESSION['login']))) {
            if ( ! $throwException) {
                return false;
            }

            throw new AccessException('Unauthorized.', 401);
        }

        $this->user = $user;
        return true;
    }

    /**
     * @throws AccessException
     */
    public function onlyGuest()
    {
        if ($this->onlyAuthorized(false)) {
            throw new AccessException('Forbidden', 403);
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($name === 'user') {
            return $this->user;
        }

        return $this->{$name};
    }
}