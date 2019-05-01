<?php

namespace Snowdog\DevTest\Controller;

class LoginFormAction extends BaseController
{
    /**
     * LoginFormAction constructor.
     */
    public function __construct()
    {
        $this->onlyGuest();
    }

    public function execute()
    {
        require __DIR__ . '/../view/login.phtml';
    }
}