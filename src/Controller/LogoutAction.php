<?php

namespace Snowdog\DevTest\Controller;

class LogoutAction extends BaseController
{
    /**
     * LogoutAction constructor.
     */
    public function __construct()
    {
        $this->onlyAuthorized();
    }

    public function execute()
    {
        unset($_SESSION['login']);
        $_SESSION['flash'] = 'Logged out successfully';
        header('Location: /login');
    }
}